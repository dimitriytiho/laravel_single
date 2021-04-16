<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Main, Permission};
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
    protected $statusActive;

    // Массив гет ключей для поиска
    protected $queryArr = [];
    // Передать поля для вывода в index виде
    protected $thead = [];

    // Правила валидации для метода Store
    protected $validateStore = [];
    // Правила валидации для метода Update
    protected $validateUpdate = [];

    // Является наследником для связанного элемента
    protected $belongChildren = false;
    // Условие выборки, название колонки
    protected $belongColumn = 'parent_id';
    // Связанная таблица, в моделе должен быть метод с таким же названием
    protected $belongTable = '';
    // Связанный маршрут
    protected $belongRoute = '';
    // Связанный элемент, возможность удалить
    protected $belongDelete = true;

    // Связанные таблицы
    protected $relatedTables = [];

    // Связанные для удаления
    protected $relatedDelete = [];

    // Связанные методы в моделе. Многие ко многим.
    protected $relatedMethods = [];

    // Связанные таблицы. Многие к одному.
    protected $relatedManyToOne = [];

    // Чексбоксы в таблице, перечислить с массиве
    protected $checkboxInTable = [];

    // Размер картинки, по-умолчанию из config('admin.imgMaxSizeSM')
    protected $imgSize = 'imgMaxSizeSM';


    // Поля пользователя для показа
    public $userFields = [
        'name',
        'email',
        'tel',
    ];


    public function __construct(Request $request)
    {
        parent::__construct();

        // Пагинация Bootstrap
        Paginator::useBootstrap();

        // Определить мобильную версию
        $detect = new \Mobile_Detect();
        $isMobile = $detect->isMobile();
        Main::set('isMobile', $isMobile);

        $this->namespaceModels = config('add.namespace_models');
        $namespaceHelpers = $this->namespaceHelpers = config('add.namespace_helpers') . '\\Admin';
        $this->viewPath = 'admin';

        $construct = $this->construct = "{$this->namespaceHelpers}\\Construct";
        $form = $this->form = "{$this->namespaceHelpers}\\Form";

        $this->template = 'general';
        $dbSort = $this->dbSort = "{$this->namespaceHelpers}\\DbSort";
        $this->perPage = config('admin.pagination_default');
        $this->perPageQuantity = config('admin.pagination');

        $statusActive = $this->statusActive = config('add.page_statuses')[1] ?? 'active';

        $userFields = $this->userFields;


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
                        Main::getError('AppController Permission ' . auth()->user()->email, __METHOD__, true, 'error');

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


        // Кол-во элементов в некоторых таблицах
        $countTable['Form'] = DB::table('forms')->whereNull('deleted_at')->count();
        $countTable['Page'] = DB::table('pages')->whereNull('deleted_at')->count();
        $countTable['User'] = DB::table('users')->whereNull('deleted_at')->count();

        view()->share(compact('isMobile', 'imgRequestName', 'imgUploadID', 'namespaceHelpers', 'construct', 'form', 'dbSort', 'countTable', 'statusActive', 'userFields'));
    }
}
