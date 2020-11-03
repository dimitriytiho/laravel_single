<?php

namespace App\Http\Controllers;


use App\Models\Main;
use Illuminate\Http\Request;
use App\Libs\Breadcrumbs;
use Illuminate\Support\Str;

class AppController extends Controller
{
    public $statusActive;
    public $perPage;
    public $breadcrumbs;
    public $userTable = 'users';
    public $userModel = 'App\\Models\\User';


    public function __construct()
    {
        parent::__construct();

        $statusActive = $this->statusActive = config('add.page_statuses')[1] ?: 'active';
        $this->perPage = config('add.pagination');
        $this->breadcrumbs = new Breadcrumbs();

        // Строка поиска
        $searchQuery = s(request()->query('s')) ?: Main::get('search_query');

        // Только внутри этой конструкции работают некоторые методы
        $this->middleware(function ($request, $next) {

            if (auth()->check()) {

                // Сохраняем в сессию страницу с которой пользователь перешёл из админки
                $previousUrl = url()->previous();
                $adminPrefix = config('add.admin');
                if (Str::is("*{$adminPrefix}*", $previousUrl)) { // Если url не содержит админский префикс
                    session()->put('back_link_admin', $previousUrl);
                }
            }

            // Вручную аутентифицировать каждого пользователя как тестового
            /*if (!auth()->check()) {
                $user = $this->userModel::find(4);
                auth()->login($user);
            }*/

            return $next($request);
        });

        // Удалить все кэши
        //cache()->flush();

        // Передаём в виды
        view()->share(compact('statusActive', 'searchQuery'));
    }
}
