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
    use HasFactory, Notifiable;
    use SoftDeletes;

    protected $guarded = ['id', 'role_id', 'created_at', 'updated_at'];

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
        return $this->belongsToMany(Role::class);
    }


    public function forms() {
        return $this->hasMany(Form::class, 'user_id', 'id');
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
            $roleAdmin = $roles[0]->title;
            foreach ($roles as $key => $role) {
                if ($roleAdmin === $role->title) {
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
    public function rolesIds() {
        $roles = $this->roles;
        $ids = [];
        if (!empty($roles[0])) {
            foreach ($roles as $key => $role) {
                $ids[] = $role;
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
}
