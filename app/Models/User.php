<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Mail\SendServiceMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'tel',
        'password',
        'address',
        'entrance',
        'apartment',
        'intercom',
        'floor',
        'accept',
        'img',
        'ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    // Обратная многие ко многим
    public function roles() {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')->withTimestamps();
    }


    public function forms() {
        return $this->hasMany(Form::class, 'user_id', 'id');
    }

    public function orders() {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }



    // Меняем шаблон письма при сбросе пароля
    public function sendPasswordResetNotification($token)
    {
        $title = __('s.link_to_change_password');
        $values = [
            'title' => __('s.you_forgot_password'),
            'btn' => __('s.reset_password'),
            'link' => route('password.reset', $token),
        ];
        $this->notify(new SendServiceMail($title , null, $values, 'service'));
    }



    /********************** Дополнительные методы **********************/


    // Проверить роли пользователей, которым разрешена админка. Возвращает true или false.
    /**
     * @return bool
     *
     * Проверяет пользователя, есть ли у него доступ в зону admin. Возвращает true или false.
     */
    public function Admin() {
        $roles = $this->roles;
        if ($roles) {
            foreach ($roles as $key => $role) {
                if ($role->area === config('admin.user_areas')[2]) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @return bool
     *
     * Проверить пользователя с ролью админ. Возвращает true или false.
     */
    public function isAdmin() {
        $roles = $this->roles;
        if (!empty($roles[0])) {
            $roleAdminId = $roles[0]->roleAdminId();
            foreach ($roles as $key => $role) {
                if ($roleAdminId == $role->id) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @return array
     *
     * Возвращает все id ролей пользователя в массиве.
     */
    public function thisUserRolesIds() {
        $roles = $this->roles;
        $ids = [];
        if (!empty($roles[0])) {
            foreach ($roles as $key => $role) {
                $ids[] = $role;
            }
        }
        return $ids;
    }


    /**
     * @return array
     *
     * Возвращает все id пользователей из переданных ролей.
     * $roles - массив с id ролей.
     */
    public function userIdsOfRoles($roles) {
        $ids = [];
        if ($roles) {
            $roles = Role::with('users')->whereIn('id', $roles)->get();
            foreach ($roles as $key => $role) {
                if ($role->users->count()) {
                    foreach ($role->users as $user) {
                        $ids[] = $user->id;
                    }
                }
            }
        }
        return $ids;
    }


    // Записать IP текущего пользователя.
    public function saveIp()
    {
        $this->ip = request()->ip();
        $this->update();
    }


    // Записать роль гость для пользователя.
    public function saveRoleGuest()
    {
        $this->roles()->sync([$this->roleGuestId()]);
    }


    // Записать роль зарегистрированный пользователь для пользователя.
    public function saveRoleUser()
    {
        $this->roles()->sync([$this->roleUserId()]);
    }


    /********************** Дополнительные статичные методы **********************/


    /**
     *
     * @return object
     *
     * Возвращает объект пользователя.
     * $email - email пользователя.
     */
    public static function getUserStatic($email)
    {
        if ($email) {
            return self::where('email', $email)->first();
        }
        return null;
    }


    /**
     *
     * @return bool
     *
     * Записывает IP пользователя. Возвращает true или false.
     * $user_id_or_email - id или email пользователя.
     * $ip - IP пользователя.
     */
    public static function saveIpStatic($user_id_or_email, $ip = null)
    {
        if ($user_id_or_email) {

            $column = is_int($user_id_or_email) ? 'id' : 'email';
            $user = self::where($column, $user_id_or_email)->first();
            $user->ip = $ip ?: request()->ip();
            $user->update();
            return true;
        }
        return false;
    }



    /********************** Методы ролей **********************/


    /**
     *
     * @return array
     *
     * Возвращает все разрешения ролей пользователя в массиве.
     * Разрешения в формате Admin\User.
     */
    public function permission()
    {
        $permission = [];
        $roles = $this->roles()->with('permission')->get();
        if ($roles->count()) {
            foreach ($roles as $role) {
                if ($role->permission->count()) {
                    foreach ($role->permission as $item) {
                        $permission[] = $item->permission;
                    }
                }
            }
        }
        return $permission;
    }


    /**
     *
     * @return bool
     *
     * Проверяет разрешен ли пользователю переданый элемент.
     * Если роль admin, то всегда разрешено.
     * Разрешения в формате Admin\User.
     */
    public function checkPermission($permission)
    {
        if ($this->isAdmin()) return true;
        return $permission && in_array($permission, $this->permission());
    }


    /**
     *
     * @return object
     *
     * Возвращает все роли в объекте.
     */
    public function getRoles()
    {
        $roles = new Role();
        return $roles->roles();
    }


    /**
     *
     * @return string
     *
     * Возвращает название роли Администратора.
     */
    public function roleAdminTitle()
    {
        $roles = new Role();
        return $roles->roleAdminTitle();
    }


    /**
     *
     * @return int
     *
     * Возвращает id роли Администратора в БД.
     */
    public function roleAdminId()
    {
        $roles = new Role();
        return $roles->roleAdminId();
    }

    /**
     *
     * @return int
     *
     * Возвращает id роли Гостя в БД.
     */
    public function roleGuestId()
    {
        $roles = new Role();
        return $roles->roleGuestId();
    }


    /**
     *
     * @return int
     *
     * Возвращает id роли Зарегистрированного пользотеля в БД.
     */
    public function roleUserId()
    {
        $roles = new Role();
        return $roles->roleUserId();
    }


    /**
     *
     * @return array
     *
     * Возвращает в объкте id ролей пользователей с доступом в админку.
     *
     * Если нужно получить id ролей пользователей без доступа в админку (с другой area), то:
     * $area - название зоны, для которой нужны id ролей, по-умолчанию admin, необязательный параметр.
     */
    public function roleAdminIds($area = null)
    {
        $roles = new Role();
        return $roles->roleAdminIds($area);
    }
}
