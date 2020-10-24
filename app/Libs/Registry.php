<?php

// Паттерн Реестр, даёт возможность сохранять данные, которые достутны в любом месте приложения.

namespace App\Libs;


class Registry
{
    use TSingleton;

    public static $properties = [];


    // Положить свойство (имя, значение)
    public function set($name, $value)
    {
        self::$properties[$name] = $value;
    }


    // Получить существующие свойство (имя)
    public function get($name)
    {
        if (isset(self::$properties[$name])) {
            return self::$properties[$name];
        }
        return null;
    }


    // Получить все свойства
    public function getAll()
    {
        return self::$properties;
    }
}
