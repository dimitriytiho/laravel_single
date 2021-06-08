<?php


namespace App\Helpers;

use Illuminate\Support\Facades\File as SupportFile;
use Illuminate\Support\Facades\Storage;

class File
{
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
     * @return string
     *
     * Возвращает строкой содержимое веб-страницы.
     * $url - URL веб-страницы.
     */
    public static function getDataFromUrl($url) {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $timeout = 5;
            $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        return null;
    }



    /*
     * Сохраняем файл через URL.
     *
     * $file - URL файла, который нужно сохранить.
     * $path - путь к папке, куда сохранить файл.
     */
    public static function saveFile($file, $path) {
        if ($file && $path) {
            $name = pathinfo($file)['basename'] ?? null;
            $path = $path . '/' . $name;

            $ch = curl_init($file);
            $fp = fopen($path, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }
    }



    /*
     * Отдаём файл на скачивание через браузер.
     * $file - полный путь к файлу
     */
    public static function fileDownload($file) {
        if (file_exists($file)) {

            // Сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт, если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // Заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));

            // Читаем файл и отправляем его пользователю
            exit(readfile($file));
        }
        return null;
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
        return null;
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
