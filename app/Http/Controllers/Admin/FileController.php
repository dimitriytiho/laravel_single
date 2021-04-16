<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\Img;
use App\Traits\TAdminBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class FileController extends AppController
{
    // Массив гет ключей для поиска
    protected $queryArr = [
        'name',
        'path',
        'old_name',
        'created_at',
        'id',
    ];


    // Передать поля для вывода в index виде
    protected $thead = [
        'path' => 'file', // Превью файла
        'name' => null,
        'old_name' => null,
        'created_at' => 't',
        'id' => null,
    ];


    use TAdminBaseController;


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Сохраняем данные в переменную
        $exts = $request->ext ?: 0;
        $exts = config('admin.images_ext')[$exts] ?? null;
        $webp = $request->webp ? true : false;

        // Валидация данных
        $request->validate(['files' => 'required']);

        $dateDir = date('Y_m');
        $dir = 'file/' . $dateDir;
        $dirFull = public_path($dir);

        // Создадим папку если нет
        File::ensureDirectoryExists($dirFull);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                $mime = $file->getMimeType();
                $size = $file->getSize();
                $ext = $file->getClientOriginalExtension();
                $nameOld = $file->getClientOriginalName();
                $name = Str::lower(Str::random()) . '.' . $ext;
                $path = $dir . '/' . $name;


                // Если картинка
                $img = $ext === 'jpeg' || $ext === 'jpg' || $ext === 'png';

                if (empty($exts[0]) && $img) {
                    $width = empty($exts[1]) ? null : (int)$exts[1];
                    $height = empty($exts[2]) ? null : (int)$exts[2];
                    $crop = !empty($exts[3]) && $exts[3] === 'square';

                    // Ресайз картинки
                    $imgResize = Image::make($file->getRealPath());


                    // Ресайз в квадрат
                    if ($crop && $imgResize->width() > $width || $crop && $imgResize->height() > $height) {
                        /*$width = $imgResize->width() > $width ? $width : $imgResize->width();
                        $height = $imgResize->height() > $height ? $height : $imgResize->height();

                        if ($imgResize->width() < $imgResize->height()) {
                            $width = $height;
                            $height = $width;
                        }

                        // Ресайз картинку к нужному размеру
                        $imgResize->resize($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                        });*/

                        $imgResize->fit($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                        });


                    // Ресайз с одной стороны
                    } else {

                        $width = $imgResize->width() > $width ? $width : $imgResize->width();
                        $height = $imgResize->height() > $height ? $height : $imgResize->height();

                        $imgResize->resize($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }

                    // Сохраняем картинку
                    $imgResize->save(public_path($path));

                    // Скопировать картинку Webp
                    if ($webp) {
                        Img::copyWebp($path);
                    }


                // Если файл
                } else {

                    // Сохранить файл
                    $file->move($dirFull, $name);
                }


                // Сохранить в БД
                $data = [
                    'name' => $name,
                    'path' => $path,
                    'ext' => $ext,
                    'mime_type' => $mime,
                    'size' => $size,
                    'old_name' => $nameOld,
                ];
                $this->model::create($data);
            }

            // Сообщение об успехе
            return redirect()
                ->route("admin.{$this->route}.index")
                ->with('success', __('a.upload_success'));
        }

        // Сообщение что-то пошло не так
        return redirect()
            ->route("admin.{$this->route}.index")
            ->withErrors(__('s.whoops'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);

        // Транзакция на 2 попытки
        DB::transaction(function () use ($id, $values) {

            // Удаляем связи
            DB::table('fileables')
                ->where('file_id', $id)
                ->delete();

            // Удаляем элемент
            $values->delete();
        }, 2);

        // Удалить файл
        if (File::exists(public_path($values->path))) {
            File::delete(public_path($values->path));
        }

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('success', __('s.removed_successfully', ['id' => $values->id]));
    }
}
