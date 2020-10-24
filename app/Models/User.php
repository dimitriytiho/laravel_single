<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Mail\SendServiceMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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


    protected $class;
    protected $model;
    protected $table;
    protected $view;
    protected $lang;



    // Расширяем модель
    public function __construct()
    {
        parent::__construct();

        $this->class = class_basename(__CLASS__);
        $this->model = "\App\Models\\{$this->class}";
        $this->table = with($this)->getTable();
        $this->view = Str::snake($this->class);
        $this->lang = lang();
    }



    // Обратная связь один ко многим
    public function role() {
        return $this->belongsTo(RoleModel::class);
    }


    // Меняем шаблон письма при сбросе пароля
    public function sendPasswordResetNotification($token)
    {
        $title = __("{$this->lang}::f.link_to_change_password");
        $values = [
            'title' => __("{$this->lang}::f.you_forgot_password"),
            'btn' => __("{$this->lang}::f.reset_password"),
            'link' => route('password.reset', $token),
        ];
        $this->notify(new SendServiceMail($title , null, $values, 'service'));
    }



    /********************** Дополнительные методы **********************/


    // Проверить роли пользователей, которым разрешена админка. Возвращает true или false.
    /**
     * @return bool
     *
     * Проверяет переданного пользователя, является ли он админом или редактором.
     */
    public function Admin() {
        return $this->role->area === config('admin.user_areas')[2];
    }


    // Проверить пользователя с ролью админ. Возвращает true или false.
    /**
     * @return bool
     *
     * Проверяет переданного пользователя, является ли он админом или редактором.
     */
    public function isAdmin() {
        return $this->role->name === config('admin.user_roles')[3];
    }


    // Проверить пользователя с ролью кассир. Возвращает true или false.
    /**
     * @return bool
     *
     * Проверяет переданного пользователя, является ли он админом или редактором.
     */
    public function cashier() {
        $roles = config('admin.user_roles');
        $cashier = $roles[5] ?? null;
        return $this->role->name === $cashier;
    }


    // Возвращает id роли администратора.
    /**
     * @return int
     *
     * Проверяет переданного пользователя, является ли он админом или редактором.
     */
    public function getRoleIdAdmin() {
        return 3;
    }


    /**
     * @return object
     *
     * Возвращает объект пользователя. Принимает email пользователя.
     */
    public function getUser($email)
    {
        //return DB::table($this->table)->where('email', $email)->first();
        return $this->model::where('email', $email)->first();
    }


    /**
     * @return bool
     *
     * Проверяет переданного пользователя, является ли он админом или редактором.
     */
    public function getAdmin($user)
    {
        return $user->role->area === config('admin.user_roles')[3];
    }


    /**
     * @return bool
     *
     * Проверяет администратора с ограниченными правами.
     */
    public function adminLimited()
    {
        // Id ролей админстраторов в config('admin.user_roles')
        $idAdmin = [
            3,
            4
        ];
        return $this->Admin() && !in_array($this->role->id, $idAdmin);
    }



    // Записать IP текущего пользователя.
    public function saveIp()
    {
        $this->ip = request()->ip();
        $this->save();
    }


    /********************** Дополнительные статичные методы **********************/


    // Возвращает объект пользователя. Принимает email пользователя.
    public static function getUserStatic($email)
    {
        if ($email) {
            $self = new self();
            return $self->model::where('email', $email)->first();
        }
        return false;
    }


    /*
     * Записывает IP пользователя.
     * $user_id_or_email - id или email пользователя.
     * $ip - IP пользователя.
     */
    public static function saveIpStatic($user_id_or_email, $ip)
    {
        if ($user_id_or_email && $ip) {
            $column = is_int($user_id_or_email) ? 'id' : 'email';
            $user = DB::table('users')->where($column, $user_id_or_email);
            if ($user->update(['ip' => $ip])) {
                return true;
            }
        }
        return false;
    }


    /*
     * Возвращает в массиве id ролей пользователей с доступом в админку.
     *
     * Если нужно получить id ролей пользователей без доступа в админку (с другой area), то:
     * $idArea - id area, для которой нужны id ролей, необязательный параметр.
     */
    public static function roleIdAdmin($idArea = 2)
    {
        $area = config('admin.user_areas')[$idArea];
        if ($area) {
            $cacheName = "roles_{$area}_ids";

            // Взязь из кэша
            if (cache()->has($cacheName)) {
                return cache()->get($cacheName);

            } else {

                // Запрос в БД
                $ids = DB::table('roles')->where('area', config('admin.user_areas')[$idArea])->pluck('id')->toArray();
                if ($ids) {

                    // Кэшируется запрос
                    cache()->forever($cacheName, $ids);

                    return $ids;
                }
            }
        }
        return [];
    }


    // Возвращает массив id всех, у кого доступ к админке
    public static function adminEditorAllId()
    {
        $self = new self();
        $adminEditor = self::roleIdAdmin();
        $cacheName = __FUNCTION__;

        // Взязь из кэша
        if (cache()->has($cacheName)) {
            return cache()->get($cacheName);

        } else {

            // Запрос в БД
            $ids = DB::table($self->table)->whereIn('role_id', $adminEditor)->pluck('id')->toArray();
            if ($ids) {

                // Кэшируется запрос
                cache()->forever($cacheName, $ids);

                return $ids;
            }
        }
        return [];
    }


    // Проверяет есть ли у авторизированного пользователя доступ в админку
    public static function isAdminEditor()
    {
        $adminEditor = self::roleIdAdmin();
        $userRole = auth()->check() ? auth()->user()->role_id : null;

        return $adminEditor && $userRole && in_array($userRole, $adminEditor);
    }
}
