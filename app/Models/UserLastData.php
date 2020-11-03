<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLastData extends App
{
    protected $table = 'users_last_data';
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     *
     * @return bool
     *
     * Сохраняем предыдущие данные пользователя.
     * $user - принимает объект модели User.
     */
    public static function saveLastUser($user)
    {
        if (!empty((int)$user->id)) {

            $userData = $user->toArray();

            // Удалим id
            unset($userData['id']);

            $last = new UserLastData();
            $last->fill($userData);

            // Сохраним user_id
            $last->user_id = $user->id;

            $last->save();
            return true;
        }
        return false;
    }


    /**
     *
     * @return bool
     *
     * Сохраняем предыдущие данные, если данные были изменены.
     * $user - принимает объект модели User.
     */
    public static function diffSaveLastUser($user)
    {
        if (!empty((int)$user->id)) {
            $lastUser = User::find($user->id);

            if ($lastUser && $lastUser->count()) {
                $lastUser = $lastUser->toArray();
                $collection = collect($lastUser);
                $user = $user->toArray();
                if (isset($user['roles'])) unset($user['roles']);
                $diff = $collection->diff($user);

                if ($diff->all()) {

                    $last = new UserLastData();
                    $last->fill($lastUser);

                    // Сохраним user_id
                    $last->user_id = $user['id'];

                    $last->save();
                    return true;
                }
            }
        }
        return false;
    }
}
