<?php

namespace App\Http\Controllers;


use App\Models\Main;
use Illuminate\Http\Request;
use App\Libs\Breadcrumbs;

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
        /*$this->middleware(function ($request, $next) {
            $authCheck = auth()->check();

            // Вручную аутентифицировать каждого пользователя как тестового
            if (!$authCheck) {
                $user = $this->userModel::find(1);
                auth()->login($user);
            }

            //View::share(compact('authCheck'));
            return $next($request);
        });*/

        // Удалить все кэши
        //cache()->flush();

        // Передаём в виды
        view()->share(compact('statusActive', 'searchQuery'));
    }
}
