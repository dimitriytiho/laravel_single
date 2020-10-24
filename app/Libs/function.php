<?php

use App\Models\Main;


/*
 * Возвращает распечатку массива.
 * $admin - передать true, для показа только админам.
 * $die - передать true, чтобы завершить работу скрипта.
 */
function du($arr, $admin = false, $die = false) {

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
    return auth()->check() && auth()->user()->Admin();
}


/**
 *
 * @return string
 *
 * Возвращает пространство имён для переводов.
 */
function lang() {
    $modulesNamespace = config('modules.namespace');
    $modulesLang = config('modules.lang');

    if ($modulesNamespace && $modulesLang) {
        return "{$modulesNamespace}\\{$modulesLang}";
    }
    return '';
}


/**
 *
 * @return string
 *
 * Возвращается переводную фразу, если её нет, то строку.
 * $str - строка для перевода.
 * $fileLang - имя файла с переводом (без .php), по-умолчанию t(t.php), необязательный параметр.
 */
function l($str, $fileLang = 't')
{
    if ($str) {
        $lang = lang();
        return \Lang::has("{$lang}::{$fileLang}.{$str}") ? __("{$lang}::{$fileLang}.{$str}") : $str;
    }
    return '';
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
    if ($routeName) {
        $route = $parameter ? route($routeName, $parameter) : route($routeName);
        return \Route::has($routeName) ? $route : '/';
    }
    return false;
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
    return '';
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
 * Возвращает цену в нужном формате (с пробелами после 3 символов, в конце знак валюты).
 * $price - цена, число или строка.
 * $currency - знак валюты, необязательный параметр, по-умолчанию рубль.
 */
function priceFormat($price, $currency = '&#8381;') {
    if ($price) {
        $currency = "&nbsp;<small>{$currency}</small>";
        return number_format(intval($price), 0, ',', '&nbsp;') . $currency;
    }
    return '';
}