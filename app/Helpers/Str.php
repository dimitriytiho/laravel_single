<?php


namespace App\Helpers;


class Str
{
    /**
     *
     * @return string
     *
     * Возвращается массив из строки.
     * $str - строка, в которой через запятую написаны слова. Эти слова разбиваются по запятой или другому делителю.
     * $delimiter - делитель, необязательный параметр, , по-умолчанию.
     */
    public static function strToArr($str, $delimiter = ',')
    {
        if ($str) {
            $str = str_replace(' ', '', $str);
            return explode($delimiter, $str);
        }
        return '';
    }


    /**
     *
     * @return string
     *
     * Возвращает строку из массива.
     * Принимает массив, далее он собирается в строку по запятой.
     */
    public static function arrToStr($arr)
    {
        if ($arr && is_array($arr)) {
            $str =  implode(', ', $arr);
            return rtrim($str, ', ');
        }
        return '';
    }


    /**
     *
     * @return string
     *
     * Сегмент от строки.
     * $str - строка, которая разбивается по делителю на сегменты.
     * $segment - номер сегмента, который нужно вернуть, по-умолчанию 0, необязательный параметр.
     * $delimiter - делитель, по-умолчанию /, необязательный параметр.
     */
    public static function strToSegment($str, $segment = 0, $delimiter = '/')
    {
        if ($str) {
            $arr = explode($delimiter, $str);
            return $arr[(int)$segment] ?? '';
        }
        return '';
    }


    /**
     *
     * @return string
     *
     * Возвращает строку.
     * Заменяет html сущности тире и пробел на эти знаки (&#8209; на -, &nbsp; на пробел).
     * $str - строка.
     */
    public static function removeTag($str)
    {
        return str_replace(['&#8209;', '&nbsp;'], ['-', ' '], $str);
    }


    /**
     *
     * @return string
     *
     * Возвращает строку.
     * Преобразовать строку в snake_case из snake-case, заменяет в строке - на _.
     */
    public static function snakeCase($str)
    {
        return str_replace('-', '_', $str);
    }
}
