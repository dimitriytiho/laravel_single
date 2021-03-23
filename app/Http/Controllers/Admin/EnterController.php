<?php

namespace App\Http\Controllers\Admin;

use App\Models\Main;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EnterController extends AppController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        // Только внутри этой конструкции работают некоторые методы
        $this->middleware(function ($request, $next) {

            // Если пользователь аутентифицирован как админ или редактор, то случиться редирект
            if (auth()->check() && auth()->user()->Admin()) {
                return redirect()->route('admin.main');
            } /*elseif (auth()->check()) {
                return redirect()->route('index');
            }*/

            return $next($request);

        });

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $view = $this->view = Str::snake($this->class);

        view()->share(compact('class', 'c', 'view'));
    }


    public function index(Request $request)
    {
        // Сообщение об открытой странице входа
        Main::getError('Open the Admin login page', __METHOD__, false, 'warning');

        $title = __('s.login');
        return view('admin.enter.index', compact('title'));
    }


    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        // Если есть ключ Recaptcha и не локально запущен сайт
        if (config('add.env') !== 'local' && config('add.recaptcha_public_key')) {
            $rules += [
                'g-recaptcha-response' => 'required|recaptcha',
            ];
        }

        $request->validate($rules);
    }


    // Действия после успешной авторизации
    protected function authenticated(Request $request, $user)
    {
        // Записать ip пользователя в БД
        $user->saveIp();

        // Сохранить сообщение об совершённом входе в админку
        Log::info('Authorization of user with access Admin. ' . Main::dataUser());
    }


    // Редирект поле авторизации
    protected function redirectPath()
    {
        return route('admin.main');
    }
}
