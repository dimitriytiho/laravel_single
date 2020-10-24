<?php


namespace App\Helpers;

use Curl\Curl;
use Illuminate\Support\Facades\File as SupportFile;
use Illuminate\Support\Facades\Storage;

class File
{
    /*
     * Соединяет файлы в один.
     *
     * $filesPathArr - массив с путём и названием файлов, относительно диска $diskName, который указан в /config/filesystems.php 'disks'.
     * Также можно передать url библиотеки, к примеру jQuery.
     *
     * $newFilePath - Путь с названием, относительно диска $diskName, который указан в /config/filesystems.php 'disks'.
     * $diskName - название диска, который указан в /config/filesystems.php 'disks', необязательный параметр, по-умолчанию папка public, можно передать null.
     * $writeFile - передайте true, если не нужно каждый раз записывать новый файл, необязательный параметр (если в файле .env APP_ENV=local, то будет каждый раз записывать новый файл).
     */
    public static function merge($filesPathArr, $newFilePath, $diskName = 'public_folder', $writeFile = false)
    {
        $part = '';
        $diskName = $diskName ?: 'public_folder';
        $writeFile = $writeFile || config('add.env') === 'local';
        $disk = Storage::disk($diskName);


        if (!$disk->exists(($newFilePath)) || $writeFile) {

            if ($filesPathArr && is_array($filesPathArr)) {

                foreach ($filesPathArr as $key => $file) {

                    // Если передаётся url, то получим содержимое
                    if (strpos($file, 'http') !== false) {

                        // Библиотека php-curl-class
                        $curl = new Curl();
                        $curl->get($file);
                        $part .= $curl->response . PHP_EOL;
                    }

                    if ($disk->exists($file)) {
                        $part .= $disk->get($file) . PHP_EOL;
                    }
                }

                // Запишем новый файл
                $disk->put($newFilePath, $part);
            }
        }
    }


    /*
     * Обычный php массив сохраняем в файл.
     * $filePath - Путь к файлу, в который сохранить массив.
     * $arr - Массив данных.
     */
    public static function arrayToFile($filePath,array $arr)
    {
        if ($arr) {

            // Формируем файл
            $part = "<?php\n\n";
            $part .= "return [\n\n";
            foreach ($arr as $k => $v) {
                $part .= "\t'{$k}' => '{$v}',\n";
            }
            $part .= "\n];\n";

            // Сохраняем данные в файл
            if (SupportFile::isFile($filePath)) {
                SupportFile::put($filePath, $part);

                return true;
            }
        }
        return false;
    }


    /**
     *
     * @return array
     *
     * Возвращает массив со всеми файлами из папки.
     * $dir - путь к папке, которая сканируется.
     * $delete - в массиве передайте название файлов, которые удалить из полученного массива, по-умолчанию . точка, .. две точки и .DS_Store, необязательный параметр.
     */
    public static function scanDir($dir, $delete = ['.', '..', '.DS_Store'])
    {
        if (is_dir($dir)) {
            $arr = scandir($dir);
            if (!empty($delete) && is_array($delete)) {
                foreach ($delete as $v) {
                    if (in_array($v, $arr)) unset($arr[array_search($v, $arr)]);
                }
            }
            return $arr;
        }
        return [];
    }


    /**
     *
     * @return string
     *
     * Возвращаем уникальное название файла, название будет таким: product_1_07-03-2020_16-31.png, где 1 это кол-во сохранёных катринок с одним и тем же названием.
     * $path - Путь до файла.
     * $name - Начальное название файла.
     * $extension - расширение файла, например .png.
     * $date - если в конце названия нужна дата, то передайте её, необязательный параметр.
     * $count - кол-во файлов с этим названием, меняется рекурсивно, необязательный параметр.
     */
    public static function nameCount($path, $name, $date = null, $count = 1)
    {
        if ($path && $name) {

            $dateIsset = $date ? "_{$date}" : null;
            $nameCount = "{$name}_{$count}";

            // Если есть файл с этим же именем, то рекурсивно вызываем этот метод пока имя не станет уникальным, прибавляя 1 к названию
            if (is_file($path . $nameCount . $dateIsset)) {

                $count = $count + 1;
                return self::nameCount($path, $name, $date, $count);

            // Если нет файла с этим именем, то запишем в название уникальное число
            } else {

                return $nameCount . $dateIsset;
            }
        }
        return '';
    }


    /**
     *
     * @return array
     *
     * Возвращает массив, в котором ключи:
     * - total общий размер в GB;
     * - freely размер свободного пространства в GB;
     * - busy размер занятого пространства в GB;
     * - percent (процент занятого место на сервере);
     * - percent_freely (процент свободного место на сервере);
     * Если размер больше 90%, то в сессию attention запишется
     */
    public static function serverBusy()
    {
        // Общий размер сервера
        $total = disk_total_space(base_path());
        // Размер свободного пространства на сервере
        $freely = disk_free_space(base_path());
        // Размер занятого пространства на сервере
        $busy = $total - $freely;
        $percent = (int)ceil($busy / $total * 100);

        // Если размер больше 90%, то в сессию attention запишется
        if ($percent >= 90 && !session()->has('attention.server_busy')) {
            session()->push('attention.server_busy', $percent);
        }

        return [
            'total' => round($total / 1000000000, 3),
            'freely' => round($freely / 1000000000, 3),
            'busy' => round($busy / 1000000000, 3),
            'percent' => $percent,
            'percent_freely' => 100 - $percent,
        ];
    }
}
