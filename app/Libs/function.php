<?php

use App\Models\Main;
use App\Helpers\Admin\Img;


/*
 * Возвращает распечатку массива.
 * $die - передать true, чтобы завершить работу скрипта.
 * $admin - передать true, для показа только админам.
 */
function du($arr, $die = false, $admin = false) {

    if ($admin && admin()) {
        echo '<pre>' . PHP_EOL . print_r($arr, true) . PHP_EOL . '</pre>';
        if ($die) die;

    } elseif (!$admin) {

        echo '<pre>' . PHP_EOL . print_r($arr, true) . PHP_EOL . '</pre>';
        if ($die) die;
    }
}


/**
 *
 * @return bool
 *
 * Проверяет роль Админ, возвращает true или false.
 */
function admin()
{
    return auth()->check() && auth()->user()->isAdmin();
}


/**
 *
 * @return bool
 *
 * Проверяет разрешен ли пользователю переданый элемент, возвращает true или false.
 * Если роль admin, то всегда разрешено.
 * Разрешения в формате User.
 *
 * $class - название класса.
 * $admin - добавляем к названию класс Admin\, если не надо, то передать null, необязательный параметр.
 */
function checkPermission($class, $admin = true) {
    return auth()->check() && auth()->user()->checkPermission(($admin ? 'Admin\\' : null) . $class);
}


/**
 *
 * @return string
 *
 * Возвращается переводную фразу, если её нет, то входную строку.
 * $str - строка для перевода.
 * $fileLang - имя файла с переводом (без .php), необязательный параметр (Сначала ищет в s.php, потом в этом файле).
 */
function l($str, $fileLang = null)
{
    if ($str) {

        if (\Lang::has("s.{$str}")) {
            return __("s.{$str}");

        } elseif ($fileLang && \Lang::has("{$fileLang}.{$str}")) {
            return __("{$fileLang}.{$str}");
        }
    }
    return $str;
}


/**
 *
 * @return string
 *
 * Возвращается маршрут, если он есть, иначе ссылка на главную.
 * $routeName - название маршрута.
 * $parameter - параметр в маршруте, необязательный параметр (если передаваемый параметр не существует, то маршрут всё равно будет возвращён).
 */
function r($routeName, $parameter = null)
{
    if ($routeName && \Route::has($routeName)) {
        return $parameter ? route($routeName, $parameter) : route($routeName);
    }
    return null;
}


/**
 *
 * @return string
 *
 * Возвращает дату в нужном формате.
 * http://userguide.icu-project.org/formatparse/datetime - Форматы дат
 *
 * $date - дата в формате: 1544636288 или 2019-07-18 13:00:00.
 * $format - формат для отображения, по-умолчанию d MMM y (j M Y) из настроек сайта, необязательный параметр.
 */
function d($date, $format = null) {
    if ($date || $date !== '0000-00-00 00:00:00') {

        // Считаются символы в дате и если 10, то формат 1544636288, если больше 10, то формат 2019-07-18 13:00:00
        if (strlen($date) > 10) {

            // Форматируем дату в метку Unix
            $date = strtotime($date);
        }

        $format = $format ?: Main::site('date_format') ?: 'd MMM y';
        $formatter = new \IntlDateFormatter(
            config('app.faker_locale'),
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            config('app.timezone'),
            \IntlDateFormatter::GREGORIAN,
            $format
        );
        return $formatter->format($date);
    }
    return null;
}


/**
 *
 * @return string
 *
 * Вырезаются html теги и допольнительные знаки.
 * $str - строка для обработки.
 * $only_strip_tags - передать true, если надо вырезаются только html теги без дополнительных знаков, необязательный параметр.
 */
function s($str, $only_strip_tags = null, $email = null)
{
    if (!$only_strip_tags) {
        $str = str_replace(['`', '/', '\\', '{', '}', ':', ';', '\'', '"', '[', ']', 'http', 'www.', 'HTTP', 'WWW.'], '*', $str);
        if (!$email) {
            $str = str_replace(['.com', '.ru', '.net', '.рф', '.su', '.ua', '.COM', '.RU', '.NET', '.РФ', '.SU', '.UA', '@' ], '*', $str);
        }
    }
    return trim(strip_tags($str, '<br>'));
}


/**
 *
 * @return int
 *
 * Возвращает телефонный номер без лишних символов, с 7 в начале и кол-во 11 символов (74951112233).
 * $phoneNumber - принимает телефонный номер (8 (495) 111-22-33).
 */
function onlyPhoneNumber($phoneNumber)
{
    $one = substr($phoneNumber, 0, 1);
    if ($one == 8) {
        $phoneNumber = 7 . substr($phoneNumber, 1);
    }
    $tel = str_replace(['+', '(', ')', '-', '_', ' '], '', $phoneNumber);
    if (strlen($tel) === 11) {
        return (int)$tel;
    }
    return $phoneNumber;
}


/**
 *
 * @return string
 *
 * Возвращает картинку Webp, если она есть.
 * Если нет картинки Webp, то вернёт ''.
 * $imagePublicPath - путь к картинке от папки public.
 */
function webp($imagePublicPath)
{
    return Img::getWebp($imagePublicPath);
}


/**
 *
 * @return string
 *
 * Возвращает цену в нужном формате (с пробелами после 3 символов, в конце знак валюты).
 * $price - цена, число или строка.
 * $currency - знак валюты, необязательный параметр, по-умолчанию рубль.
 */
function priceFormat($price, $currency = '&#8381;') {
    if ($price) {
        $currency = "&nbsp;<small>{$currency}</small>";
        return number_format(intval($price), 0, ',', '&nbsp;') . $currency;
    }
    return null;
}
