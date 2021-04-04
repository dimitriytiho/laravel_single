<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\DbSort;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RoleController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\" . $this->class;
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);

        // Формируем массив с контроллерами
        $files = [];
        $controllersPath = app_path('Http/Controllers');
        $controllers = File::allFiles($controllersPath);
        foreach ($controllers as $key => $file) {
            $path = $file->getRealPath();
            $path = Str::after($path, "{$controllersPath}/");
            $path = Str::before($path, 'Controller.php');
            $path = str_replace('/', '\\', $path);
            if ($path) {

                // Пропустим контроллеры, которые указаны в настройках
                if (in_array($path, config('admin.permission_skip_controllers'))) {
                    continue;
                }
                $files[] = $path;
            }
        }
        // Добавим в массив контроллеры, которые нет в основной папке
        $files = array_merge(config('admin.permission_add_controllers') ?: [], $files);

        // Формируем массив с маршрутами
        /*$routesNames = [];
        $currentRoute = Route::currentRouteName();
        $routes = Route::getRoutes()->get();
        foreach ($routes as $item) {
            $name = $item->getName();
            if ($name) {
                $routesNames[] = $name;
            }
        }*/


        // Связанные таблицы, которые нельзя удалить, если есть связанные элементы, а также в моделе должен быть метод с название таблицы, реализующий связь
        $relatedDelete = $this->relatedDelete = [

            // Страницы
            'users',
        ];

        view()->share(compact('class', 'c','model', 'table', 'route', 'view', 'files', 'relatedDelete'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'title',
            'area',
            'id',
        ];

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;

        // Метод для поиска и сортировки запроса БД
        $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage);

        // Передать поля для вывода, значение l - с переводом, t - дата
        $thead = [
            'title' => 'l',
            'area' => null,
            'id' => null,
        ];


        // Id элементов, которые нельзя удалять
        /*$guardedIds = $this->model::first()->guardedIds()->toArray();
        $belongIds = DB::table('role_user')->pluck('user_id')->toArray();
        $guardedIds = array_merge($guardedIds, $belongIds);*/


        $f = __FUNCTION__;
        $title = __("a.{$this->table}");
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'values', 'queryArr', 'col', 'cell', 'thead'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => "required|string|unique:{$this->table},title|max:32",
        ];
        $request->validate($rules);
        $data = $request->all();

        // Если area нет в списке, то не будем сохранять
        if (!in_array($data['area'], config('admin.user_areas'))) {
            unset($data['area']);
        }


        // Создаём экземкляр модели
        $values = new Role();

        // Заполняем модель новыми данными
        $values->fill($data);

        // Сохраняем элемент
        $values->save();

        // Сохраним разрешения
        $permission = [];
        if ($request->permission) {
            foreach ($request->permission as $item) {
                $permission[] = [
                    'role_id' => $values->id,
                    'permission' => $item,
                    'created_at' => $values->freshTimestamp(),
                    'updated_at' => $values->freshTimestamp(),
                ];
            }
        }
        Permission::insert($permission);

        // Удалить все кэши
        cache()->flush();

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.edit", $values->id)
            ->with('success', __('s.created_successfully', ['id' => $values->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
        //
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $values = $this->model::findOrFail($id);


        // Связанные
        $valuesBelong = $values->users;
        $routeBelong = 'user';

        // Если роль запрещена к изменениям
        $disabledDelete = $values->guardedIds()->contains($values->id) ? 'readonly' : null;

        $disabledIds = [];
        $selected = $values->permission->pluck('permission');

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'valuesBelong', 'routeBelong', 'selected', 'disabledDelete', 'disabledIds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);


        // Если роль запрещена к изменениям
        if ($values->guardedIds()->contains($id)) {

            // Сообщение об ошибке
            return redirect()
                ->route("admin.{$this->route}.edit", $values->id)
                ->withErrors(__('s.whoops'));
        }

        $rules = [
            'title' => "required|string|unique:{$this->table},title,{$id}|max:32",
        ];
        $request->validate($rules);
        $data = $request->all();

        // Если area нет в списке, то не будем сохранять
        if (!in_array($data['area'], config('admin.user_areas'))) {
            unset($data['area']);
        }

        // Заполняем модель новыми данными
        $values->fill($data);


        // Если в запросе элементы, которых нет в БД, то сохраним их
        $permissionInsert = [];
        $permissionOld = $values->permission;
        if ($request->permission) {
            foreach ($request->permission as $key => $item) {
                if (!$permissionOld->where('permission', $item)->count()) {
                    $permissionInsert[] = [
                        'role_id' => $id,
                        'permission' => $item,
                        'created_at' => $values->freshTimestamp(),
                        'updated_at' => $values->freshTimestamp(),
                    ];
                }
            }
            Permission::insert($permissionInsert);
        }

        // Если в запросе нет элементов, которые были в БД, то удалим их
        $permissionOld = $permissionOld->pluck('permission', 'id');
        $permissionDiff = $permissionOld->diff($request->permission);
        Permission::destroy($permissionDiff->keys());


        // Обновляем элемент
        $values->update();

        // Удалить все кэши
        cache()->flush();

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.edit", $values->id)
            ->with('success', __('s.saved_successfully', ['id' => $values->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);

        // Если есть связи, то вернём ошибку
        if (!empty($this->relatedDelete)) {
            foreach ($this->relatedDelete as $relatedTable) {
                if ($values->$relatedTable->count()) {
                    return redirect()
                        ->route("admin.{$this->route}.edit", $id)
                        ->withErrors(__('s.remove_not_possible') . ', ' . __('s.there_are_nested') . __('a.id'));
                }
            }
        }

        // Удаляем связанные разрешения
        $permission = $values->permission->pluck('id');
        Permission::destroy($permission);

        // Удаляем элемент
        $values->delete();

        // Удалить все кэши
        cache()->flush();

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('success', __('s.removed_successfully', ['id' => $values->id]));
    }
}
