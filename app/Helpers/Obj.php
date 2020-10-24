<?php


namespace App\Helpers;


class Obj
{
    /**
     *
     * @return object
     *
     * Возвращаем объект всех элементов данной модели и к каждому элементу получаем элементу из связанной таблице.
     * В объекте ключи будут id элементов.
     * Связанные элементы сортируем по сортировке.
     *
     * Внимание! Запрос кэшируется, чтобы увидеть измения, нужно удалить кэш.
     *
     * $model - Полный путь к модели.
     * $belongMethod - имя метода в связанной моделе.
     */
    public static function getBelong($model, $belongMethod)
    {
        // Имя для кэша (название класса_название метода)
        $c = class_basename($model);
        $f = __FUNCTION__;

        // Кэшируем запрос
        if (cache()->has("{$c}_{$f}")) {

            // Достать из кэша
            $ob = cache()->get("{$c}_{$f}");

        } else {

            $ob = $model::with([$belongMethod => function ($query) {
                $query->orderBy('sort');
            }])->get()->keyBy('id');
            //$ob = $model::with($belongMethod)->get()->keyBy('id');

            // Сохранить в кэш
            cache()->forever("{$c}_{$f}", $ob);
        }
        return $ob;
    }
}
