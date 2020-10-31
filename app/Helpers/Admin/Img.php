<?php


namespace App\Helpers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\ImageManagerStatic as Image;

class Img
{
    private $acceptedImages = [];

    private function __construct()
    {
        $this->acceptedImages = config('admin.acceptedImagesExt');
    }


    /**
     *
     * @return string
     *
     * Ресайз, сохранение и удаление прошлой картинки, возвращает новый путь картинки, от папки public.
     * $request - передать объкт Request.
     * $thisClass - название класса контроллера (например User).
     * $oldImage - если нужно удалить картинку, то передать путь от папки public, необязательный параметр.
     */
    public static function upload(Request $request, $thisClass, $oldImage = null)
    {
        $img = $request->file('img');
        $imgExt = $img->extension();

        // Ресайз картинки
        $imgResize = Image::make($img->getRealPath());
        $imgResize->resize(config('admin.imgMaxSizeSM'), config('admin.imgMaxSizeSM'), function ($constraint) {
            $constraint->aspectRatio();
        });

        // Сохраняем картинку
        $ImgFolder = config("admin.img{$thisClass}") . '/' . date('Y_m');

        // Создадим папку если нет
        if (!File::exists(public_path($ImgFolder))) {
            File::makeDirectory(public_path($ImgFolder));
        }

        // Уникальное имя
        $ImgName = "{$ImgFolder}/" . uniqid() . ".{$imgExt}";
        $imgResize->save(public_path($ImgName));

        // Удаляем картинку, которая была
        if ($oldImage && $oldImage !== config("admin.img{$thisClass}Default") && File::exists(public_path($oldImage))) {
            File::delete(public_path($oldImage));
        }

        return $ImgName;
    }


    // Возвращает разрешенные разрешения картинок строкой '.jpg, .jpeg, .png, .gif'
    public static function acceptedImagesExt()
    {
        $self = new self();
        return $self->acceptedImages ? '.' . implode(', .', $self->acceptedImages) : false;
    }


    // Поддерживает браузер картинки Webp, возвращает true или false.
    public static function supportWebp()
    {
        // https://github.com/pcosta94/laravel-check-webp-support/blob/master/src/helpers.php
        /*$httpAccept = request()->server('HTTP_USER_AGENT');
        return strpos($httpAccept, 'Trident') === false || strpos($httpAccept, 'Safari') === false;*/

        $httpAccept = request()->server('HTTP_ACCEPT');
        return strpos($httpAccept, 'image/webp') !== false;
    }


    /*
     * Возвращает название картинки Webp, если она есть, если её нет, то возвращает обычную картинку.
     * $imagePublicPath - путь с название обычной картинки.
     * Название картинки Webp должно быть одинаково с обычной картинкой.
     */
    public static function getWebp($imagePublicPath)
    {
        if ($imagePublicPath) {

            // Полный путь к картинке
            $pathImg = public_path($imagePublicPath);
            if (File::isFile($pathImg)) {

                // Название картинки
                $name = class_basename($imagePublicPath);

                // Вырезаем из пути название картинки
                $path = str_replace($name, '', $imagePublicPath);

                // Получаем разрешение картинки
                $ext = pathinfo($name)['extension'] ?? null;

                // Вырезаем разрешение картинки
                $name = str_replace(".{$ext}", '', $name);

                // Название картинки webp
                $webp = "{$name}.webp";

                // Добавляем к пути название webp
                $pathWebp = public_path($path . $webp);

                // Если есть webp, то возвращаем её
                if (File::isFile($pathWebp)) {
                    return $path . $webp;
                }
            }
        }
        return $imagePublicPath;
    }


    /*
     * Удалим картинку с сервера, возвращает true или false.
     * $img - название картинки, как в БД, например /img/product/tovar_1_10-03-2020_21-28.jpeg.
     * $imgDefault - картинка по-умолчанию, если не надо её удалять, то передать, например config('admin.imgProductDefault'), необязательный параметр.
     */
    public static function deleteImg($img, $imgDefault = null)
    {
        if ($img) {
            $path = public_path() . $img;
            $ifDefault = $imgDefault && $imgDefault === $img;

            if (!$ifDefault && File::isFile($path)) {

                // Удалим картинку Webp
                $webp = self::getWebp($img);
                if ($webp !== $img) {
                    File::delete(public_path() . $webp);
                }

                // Удалим обычную картинку
                File::delete($path);
                return true;
            }
        }
        return false;
    }


    /*
     * Удалим с сервера картинки галереи, принадлежащии одному элементу, к примеру товару, возвращает true или false.
     * $table - название таблице, в которой названия картинок.
     * $elementName - название элемента в таблице, к примеру product_id.
     * $elementID - id элемента, для которого картинки.
     */
    public static function deleteImgAll($table, $elementName, $elementID)
    {
        if ($table && $elementName && $elementID && Schema::hasTable($table) && Schema::hasColumn($table, $elementName)) {

            $images = DB::table($table)->where($elementName, (int)$elementID)->pluck('img');
            $images = $images->toArray();

            if ($images) {
                foreach ($images as $img) {
                    self::deleteImg($img);
                }
                return true;
            }
        }
        return false;
    }


    public static function exceptionsName($str) {
        if ($str) {
            $str = str_replace(' ', '_', $str);
            return str_replace([':', '-', '.'], '-', $str);
        }
        return false;
    }
}
