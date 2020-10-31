<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAdmin extends User
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'users';
    protected $fillable = [];
    protected $guarded = ['id', 'created_at', 'updated_at'];



    /**
     *
     * @return bool
     *
     * Если не Админ выбирает себе роль Админ.
     */
    /*public function noAdmintoAdmin()
    {
        return $this->id == auth()->user()->id && !auth()->user()->isAdmin() && $this->role_id == auth()->user()->getRoleIdAdmin();
    }*/


    /**
     *
     * @return bool
     *
     * Если не Админ редактирует Админа.
     */
    /*public function noAdminEditAdmin()
    {
        return !auth()->user()->isAdmin() && $this->getRoleIdUser() == auth()->user()->getRoleIdAdmin();
    }*/



    /**
     *
     * @return int
     *
     * Сохраним пользователя отправителя формы, возвращает id пользователя.
     * Если пользователь admin, то пароль не обновляется.
     * Если есть пользователь, то обновим его данные, если нет, то создадим с ролью guest (не зарегистрированный пользователь).
     * Если пользователь существует сохраним его прошлые данные в таблицу users_last_data.
     * $data - данные формы.
     */
    public static function saveUser(Request $request)
    {
        $data = $request->all();
        $passwordDefault = '$2y$10$0v6wawOOs/cwp.wAPmbJNe4q3wUSnBqfV7UQL7YbpTtJE0dJ8bMKK'; // 123321q - такой пароль по-умолчанию у пользователей со статусом guest (не зарегистрированный пользователь).

        $data['email'] = empty($data['email']) ? null : s($data['email'], null, true);

        // Проверяем существует ли такой пользователь
        $issetUser = User::where('email', $data['email'])->first();

        // Данные
        if (!empty($data['name'])) {
            $data['name'] = s($data['name']);
        }
        if (!empty($data['tel'])) {
            $data['tel'] = s($data['tel']);
        }
        if (!empty($data['address'])) {
            $data['address'] = s($data['address']);
        }

        // Если есть пароль, то он хэшируется
        $data['password'] = empty($data['password']) ? $passwordDefault : Hash::make($data['password']);

        // В чекбокс запишем 1
        $data['accept'] = $data['accept'] ? '1' : '0';
        $data['ip'] = $request->ip();


        // Если пользователь существует
        if ($issetUser && $issetUser->count()) {

            // Для Admin не обновляем пароль
            if ($issetUser->Admin()) {
                unset($data['password']);
            }

            // Сохраняем предыдущие данные
            UserLastData::saveLastUser($issetUser);

            $issetUser->fill($data);
            $issetUser->update();
            return $issetUser->id;

        } else {

            // Создадим нового пользователя
            $user = new User();
            $user->fill($data);
            $user->save();
            return $user->id;
        }
    }
}
