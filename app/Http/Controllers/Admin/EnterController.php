<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Main;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Validator;
use App\Mail\SendMail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EnterController extends AppController
{
    use ThrottlesLogins;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\User";
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);

        view()->share(compact('class', 'c','model', 'table', 'route', 'view'));
    }


    public function index(Request $request)
    {
        // Если пользователь аутентифицирован как админ или редактор, то случиться редирект
        if (auth()->check() && auth()->user()->Admin()) {
            return redirect()->route('admin.main');
        } /*elseif (auth()->check()) {
            return redirect()->route('index');
        }*/

        // Сообщение об открытой странице входа
        Main::getError('Open the Admin login page', __METHOD__, false, 'warning');

        $title = __('s.login');
        return view("admin.{$this->view}.index", compact('title'));
    }

    public function enterPost(Request $request)
    {
        // Сообщение о запросе
        Log::warning('Request Enter login. ' . Main::dataUser());

        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string',
            //'g-recaptcha-response' => 'required|recaptcha',
        ];
        $request->validate($rules);

        // Laravel блокирует неправильные попытки входа
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    public function username()
    {
        return 'email';
    }


    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }


    protected function guard()
    {
        return Auth::guard();
    }


    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }


    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }


    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
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
