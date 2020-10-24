<?php


namespace App\Helpers;


class Arr
{
    /**
     *
     * @return boolean
     *
     * Если входящие данные ассоциативный массив или объект Laravel и нужно проверить на пустоту.
     * $arr - массив или объект.
     */
    public static function isNotEmpty($data)
    {
        return $data && is_array($data) && array_filter($data) || $data && is_object($data) && $data->isNotEmpty();
    }


    /**
     *
     * @return array
     *
     * Безопасное удаление значения из массива.
     * $value - значение, которое удалить.
     * $arr - массив.
     */
    public static function unsetValue($value, $arr)
    {
        if ($arr && is_array($arr)) {
            if (in_array($value, $arr)) {
                unset($arr[array_search($value, $arr)]);
            }
            return $arr;
        }
        return [];
    }
}
