<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class UserAdmin extends User
{
    protected $table = 'users';
    protected $fillable = [];
    protected $guarded = ['id', 'created_at', 'updated_at'];



    /**
     *
     * @return bool
     *
     * Если не Админ выбирает себе роль Админ.
     * $roleIds - передать массив с id ролей, которые выбрал пользователь.
     */
    public function noAdminToAdmin($roleIds = [])
    {
        if ($roleIds && is_array($roleIds)) {
            return !auth()->user()->isAdmin() && in_array($this->roleAdminId(), $roleIds); //$this->id == auth()->user()->id &&
        }
        return !auth()->user()->isAdmin() && $this->isAdmin(); //$this->id == auth()->user()->id &&
    }


    /**
     *
     * @return bool
     *
     * Если не Админ редактирует Админа.
     */
    public function noAdminEditAdmin()
    {
        return !auth()->user()->isAdmin() && $this->isAdmin();
    }



    /**
     *
     * @return object
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
        $passwordDefault = Str::random(6); // Создаём рамдомный пароль
        //$passwordDefault = '$2y$10$0v6wawOOs/cwp.wAPmbJNe4q3wUSnBqfV7UQL7YbpTtJE0dJ8bMKK'; // 123321q - такой пароль по-умолчанию у пользователей со статусом guest (не зарегистрированный пользователь).

        $data['email'] = empty($data['email']) ? null : s($data['email'], null, true);

        // Проверяем существует ли такой пользователь
        $issetUser = User::withTrashed()->where('email', $data['email'])->first();

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

            // Если пользователь был удалён, то вернём его
            if ($issetUser->deleted_at) {
                $issetUser->restore();
            }

            $issetUser->fill($data);

            // Статус повторно
            $issetUser->status = config('admin.user_statuses')['1'] ?? 'info';
            
            $issetUser->update();
            return $issetUser;

        } else {

            // Создадим нового пользователя
            $user = new User();
            $user->fill($data);
            $user->save();

            // По умолчанию назначим роль Пользователь
            $user->saveRoleUser();

            // Отправить письмо пользователю
            try {
                $routeLogin = route('login');
                $title = "Вы успешно зарегистрированы на сайте " . Main::site('name');
                $body =  <<<S
<p>Логин: <strong>{$user->email}</strong></p>
<p>Пароль: <strong>{$passwordDefault}</strong></p>
<br>
<br>
<p><a href="{$routeLogin}">Вход в личный кабинет</a></p>
S;

                // Отправить письмо пользователям
                Mail::to($user->email)
                    ->send(new SendMail($title, $body));

            } catch (\Exception $e) {
                Main::getError('Error sending email', __METHOD__, false);
            }

            return $user;
        }
    }
}
