<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\LeftMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        /*dump(app_path('Http/Controllers'));
        $path = app_path('Http/Controllers');
        $files = \Illuminate\Support\Facades\File::allFiles($path);
        foreach ($files as $key => $file) {
            dump($file->getRealPath()); // Полный путь
            dump($file->isFile()); // Проверка на файл
            dump($file->isDir()); // Проверка на папку
            dump($file->getContents()); // Получить содержимое файла
            dump(pathinfo($file)); // Данные файла php функция pathinfo()
        }*/

        //dump($request->route()->action);
        //dump(Str::before($request->route()->action['controller'], '@'));

        // Только внутри этой конструкции работают некоторые методы
        $this->middleware(function ($request, $next) {

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


            return $next($request);
        });


        // Переменные для Dropzone JS
        $imgRequestName = $this->imgRequestName = null;
        $imgUploadID = $this->imgUploadID = null;

        /*view()->composer('vendor.laravel-log-viewer.log', function ($view) use ($pathPublic) {
            $view->with('pathPublic', $pathPublic);
        });*/

        view()->share(compact('imgRequestName', 'imgUploadID', 'namespaceHelpers', 'construct', 'form', 'dbSort'));
    }
}
