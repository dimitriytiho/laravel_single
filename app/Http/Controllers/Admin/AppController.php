<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\LeftMenu;
use App\Models\Main;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\Admin\Locale;
use Illuminate\Pagination\Paginator;

class AppController extends Controller
{
    protected $namespaceModels;
    protected $namespaceHelpers;
    protected $dbSort;
    protected $template;

    protected $construct;
    protected $form;
    protected $viewPath;

    protected $imgRequestName;
    protected $imgUploadID;
    protected $perPage;
    protected $perPageQuantity;

    // Связанная таблица
    protected $belongTable;

    // Связанные таблицы
    protected $relatedTables = [];

    // Связанные для удаления
    protected $relatedDelete = [];


    public function __construct(Request $request)
    {
        parent::__construct();

        // Пагинация Bootstrap
        Paginator::useBootstrap();

        $this->namespaceModels = config('add.namespace_models');
        $namespaceHelpers = $this->namespaceHelpers = config('add.namespace_helpers') . '\\Admin';
        $this->viewPath = 'admin';

        $construct = $this->construct = "{$this->namespaceHelpers}\\Construct";
        $form = $this->form = "{$this->namespaceHelpers}\\Form";

        $this->template = 'general';
        $dbSort = $this->dbSort = "{$this->namespaceHelpers}\\DbSort";
        $this->perPage = config('admin.pagination_default');
        $this->perPageQuantity = config('admin.pagination');


        // Только внутри этой конструкции работают некоторые методы
        $this->middleware(function ($request, $next) {

            $permission = collect([]);
            $isAdmin = null;
            if (auth()->check()) {
                $isAdmin = auth()->user()->isAdmin();

                // Разрешаем доспуп к контроллерам по ролям, кроме Админов
                if (!$isAdmin) {

                    $rolesIds = auth()->user()->roles()->pluck('role_id');
                    $permission = Permission::whereIn('role_id', $rolesIds)->pluck('permission');

                    // Всегда разрешает EnterController
                    $permission->push('Admin\Enter');
                    // Всегда добавляем MainController
                    $permission->push('Admin\Main');

                    $currentController = Str::before($request->route()->action['controller'], 'Controller@');
                    $currentController = Str::after($currentController, 'App\Http\Controllers\\');


                    if (!$permission->contains($currentController)) {

                        // Запишем ошибку, выбросим исключение
                        Main::getError(' Permission Controller for ' . auth()->user()->email, __METHOD__, true, 'error');

                    } /*else {

                        // Сделаем редирект на разрешённый контроллер
                        if (!empty($permission[0])) {
                            return redirect();
                        }

                    }*/

                    // Для видов сохраняем только название класса
                    $permission = $permission->map(function ($item, $key) {
                        return Str::after($item, 'Admin\\');
                    });
                }
            }



            // Устанавливаем локаль
            Locale::setLocaleFromCookie($request);


            // Сохраняем в сессию страницу с которой пользователь перешёл в админку
            $backLink = url()->previous();
            $adminPrefix = config('add.admin');
            // Если url не содержит админский префикс
            $containAdmin = Str::is("*{$adminPrefix}*", $backLink);
            $enter = config('add.enter');
            $containEnter = Str::is("*{$enter}*", $backLink);
            if (!($containAdmin || $containEnter)) {
                session()->put('back_link_site', $backLink);
            }


            view()->share(compact('isAdmin', 'permission'));

            return $next($request);
        });


        // Переменные для Dropzone JS
        $imgRequestName = $this->imgRequestName = null;
        $imgUploadID = $this->imgUploadID = null;

        /*view()->composer('vendor.laravel-log-viewer.log', function ($view) use ($pathPublic) {
            $view->with('pathPublic', $pathPublic);
        });*/


        // Кол-во элеменото в некоторых таблицах
        $countTable['Form'] = DB::table('forms')->count();
        $countTable['Page'] = DB::table('pages')->count();
        $countTable['User'] = DB::table('users')->count();
        $countTable['Order'] = DB::table('orders')->count();

        view()->share(compact('imgRequestName', 'imgUploadID', 'namespaceHelpers', 'construct', 'form', 'dbSort', 'countTable'));
    }
}
