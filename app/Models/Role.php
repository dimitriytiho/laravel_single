<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends App
{
    use HasFactory;
    use SoftDeletes;


    /*

    Роли должны быть в порядке:
    Гость
    Пользователь
    Админ

     */


    // Связь многие ко многим
    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    /**
     *
     * @return object
     *
     * Возвращает все роли в объекте.
     */
    public function roles()
    {
        // Взязь из кэша
        if (cache()->has('get_roles')) {
            return cache()->get('get_roles');

        } else {

            // Запрос в БД
            $values = self::all();

            // Кэшируется запрос
            cache()->forever('get_roles', $values);

            return $values;
        }
    }


    /**
     *
     * @return string
     *
     * Возвращает название роли Администратора.
     */
    public function roleAdminTitle()
    {
        $roles = self::roles();
        return empty($roles[2]) ? '' : $roles[2]->title;
    }


    /**
     *
     * @return int
     *
     * Возвращает id роли Администратора в БД.
     */
    public function roleAdminId()
    {
        $roles = self::roles();
        return empty($roles[2]) ? 0 : $roles[2]->id;
    }


    /**
     *
     * @return array
     *
     * Возвращает в массиве id ролей пользователей с доступом в админку.
     *
     * Если нужно получить id ролей пользователей без доступа в админку (с другой area), то:
     * $area - название зоны, для которой нужны id ролей, по-умолчанию admin, необязательный параметр.
     */
    public function roleAdminIds($area = null)
    {
        $area = $area ?: (config('admin.user_areas')[2] ?? null);
        if ($area) {
            $roles = self::roles();
            if ($roles && $roles->count()) {
                return $roles->where('area', $area)->pluck('id')->toArray();
            }
        }
        return [];
    }
}
