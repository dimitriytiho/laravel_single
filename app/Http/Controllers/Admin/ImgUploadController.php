<?php

namespace App\Http\Controllers\Admin;

use App\Models\Main;
use App\Helpers\Admin\Img;
use App\Helpers\File as helpersFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ImgUploadController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->class = str_replace('Controller', '', class_basename(__CLASS__));
    }


    public function upload(Request $request)
    {
        if ($request->isMethod('post') && $request->wantsJson()) {

            $requestAll = $request->all();
            if ($requestAll && is_array($requestAll)) {
                $class = $request->input('class');
                $route = Str::lower($class);
                $table = $request->input('table');

                // Если нет папки, то создадим её
                $disk = Storage::disk('public_folder');
                $dateDir = date('Y_m');
                $directory = config('add.img') . "/{$route}/{$dateDir}";
                $pathDirectory = config('add.imgPath') . "/{$route}/{$dateDir}";
                if (!File::isDirectory($pathDirectory)) {
                    $disk->makeDirectory($directory);
                }


                // ID элемента, для которого картинка
                $imgUploadID = (int)$request->input('imgUploadID');

                // Начальная часть названия передаваемой картинки
                $requestName = $request->input('name');

                // Если передаём одну картинку, то передать 1. Если передаём множество картинок, то передать цифру больше 1
                $maxFiles = (int)$request->input('maxFiles');

                // Множественная загрузка
                if ($maxFiles > 1) {
                    $class = "{$class}Gallery";
                    $table = "{$route}_gallery";
                }
            }

            if ($class && $imgUploadID && $request->hasFile('file') && Schema::hasTable($table)) {

                $img = $request->file('file');
                $size = $img->getSize();
                $ext = $img->extension();
                $extValidText = Img::acceptedImagesExt();
                $extValid = config('admin.acceptedImagesExt');
                //$path = $img->path();
                //$originName = $img->getClientOriginalName();

                // Если файл больше 2mb
                $maxSize = 2097152;
                $maxSizeRound = (int)round($maxSize / 1000000);
                if ($size > $maxSize) {
                    return response()->json(['answer' => __('s.maximum_file_size', ['size' => $maxSizeRound])]);
                }

                // Если разрешение файла не в разрешённых
                if (!in_array($ext, $extValid)) {
                    return response()->json(['answer' => __('s.allowed_to_upload_files') . $extValidText]);
                }

                // Путь на сервере для картинки
                $imgSavePath = config("admin.imgPath{$class}") . "/{$dateDir}/";

                // Дата картинки
                $date = Img::exceptionsName(d(time(), config('admin.date_format')));

                // Имя картинки
                //$imgName = "{$requestName}_" . Str::lower(Str::random(3)) . "_{$date}.{$ext}";
                $imgName = helpersFile::nameCount($imgSavePath, $requestName, $ext, $date);


                // Перемещаем картинку в нужное место
                $img->move($imgSavePath, "{$imgName}.{$ext}");


                // Делаем копию в Webp формате
                if (config('admin.webp')) {

                    // Определяем разрешение
                    if ($ext === 'jpeg' || $ext === 'jpg') {
                        $webp = @imagecreatefromjpeg("{$imgSavePath}{$imgName}.{$ext}");

                    } elseif ($ext === 'png') {
                        $webp = @imagecreatefrompng("{$imgSavePath}{$imgName}.{$ext}");

                    } elseif ($ext === 'gif') {
                        $webp = @imagecreatefromgif("{$imgSavePath}{$imgName}.{$ext}");
                    }

                    // Копируем webp картинку
                    if (!empty($webp)) {
                        $webpName = "{$imgName}.webp";
                        imagepalettetotruecolor($webp);
                        imagealphablending($webp, true);
                        imagesavealpha($webp, true);
                        imagewebp($webp, $imgSavePath . $webpName, config('admin.webpQuality'));
                        imagedestroy($webp);
                    }
                }


                // Путь URL для новой картинки
                $imgNewPaht = config("admin.img{$class}") . "/{$dateDir}/{$imgName}.{$ext}";

                // Одиночная загрузка
                if ($maxFiles <= 1) {

                    // Получаем данные из БД
                    $oldSql = DB::table($table)->find($imgUploadID);

                    // Удаляем старый файл из папки на сервере, кроме картинки по-умолчанию
                    $oldImg = $oldSql->img ?? null;
                    Img::deleteImg($oldImg, config("admin.img{$class}Default"));

                    // Сохраняем картинку в БД для одиночной загрузки
                    $sql = DB::table($table)->where('id', $imgUploadID)->update(['img' => $imgNewPaht]);

                    /*$res = [
                        'answer' => 'success',
                        'name' => $sql,
                        'href' => '',
                    ];
                    return response()->json($res);*/

                // Множественная загрузка
                } else {

                    // Сохраняем картинки в БД
                    $insertData = [
                        ["{$route}_id" => $imgUploadID, 'img' => $imgNewPaht],
                    ];
                    $sql = DB::table($table)->insert($insertData);
                }

                if ($sql) {

                    // Ответ для JS
                    $res = [
                        'answer' => 'success',
                        'name' => "{$imgName}.{$ext}",
                        'href' => $imgNewPaht,
                        //'test' => "{$imgName}.{$ext}",
                    ];
                    return response()->json($res);
                }


                // Если не сохранено в БД, то удалим файл
                Img::deleteImg("{$imgSavePath}{$imgName}.{$ext}");

            }
            return response()->json(['answer' => __('s.whoops')]);
        }
        Main::getError('Request No Post', __METHOD__);
    }


    public function remove(Request $request)
    {
        if ($request->ajax()) {
            $table = $request->table;
            $class = $request->class;
            $route = Str::lower($class);
            $img = $request->img;
            $maxFiles = $request->maxFiles;
            $defaultImg = config("admin.img{$class}Default");

            // Множественная загрузка
            if ($maxFiles > 1) {
                $class = "{$class}Gallery";
                $table = "{$route}_gallery";
            }

            if ($table && $class && $img && $maxFiles && $img !== $defaultImg && Schema::hasTable($table)) {

                // Если одиночная загрузка картинок, то заменим название на название по-умолчанию
                if ($maxFiles <= 1) {

                    // Если меняется картинка пользователя, то заменим её в объекте auth
                    /*if ($class === 'User') {
                        auth()->user()->update(['img' => $defaultImg]);
                    }*/

                    // Обновим ячейку в БД
                    $sql = DB::table($table)->where('img', $img)->update(['img' => $defaultImg]);


                // Если множественная загрузка картинок, то удалим ряд записи
                } else {
                    $sql = DB::table($table)->where('img', $img)->delete();
                }


                if ($sql) {

                    // Удалим картинку с сервера
                    Img::deleteImg($img);

                    // Ответ JS
                    return __('s.removed_successfully_name', ['name' => $img]);
                }

            } else {
                return __('s.whoops');
            }
        }
        Main::getError('Request No Ajax', __METHOD__);
    }


    /*
     * Удаляет картинку и webp картинку если она есть.
     * В БД заменяется картинка на по-умолчанию.
     *
     * Передать в ссылке:
     * &token=token - токен.
     * &img=/img_path.jpg - путь с название картинки от public.
     * &default=/img/default/no_image.jpg - картинка по-умолчанию.
     * &table=products - название таблицы.
     * &id=id - id ряда или элемента в БД, в котором картинка.
     * &input=img - названи колонки в БД, по-умолчанию img, необязательный параметр.
     */
    public function deleteImg(Request $request) {
        $token = $request->token;
        $img = $request->img;
        $default = $request->default;
        $table = $request->table;
        $id = $request->id;
        $input = $request->input ?: 'img';
        if ($img && $default && $table && $id && $token === csrf_token() && Schema::hasTable($table)) {

            DB::table($table)
                ->where($input, $img)
                ->update([$input => $default]);

            Img::deleteImg($img);
        }
        return redirect()->back();
    }
}
