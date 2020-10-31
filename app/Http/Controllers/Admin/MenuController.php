<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\DbSort;
use App\Models\Menu;
use App\Models\MenuName;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends AppController
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

        view()->share(compact('class', 'c','model', 'table', 'route', 'view'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Записать в куку id из привязанной таблице, если не записано
        $currentParentId = $request->cookie("{$this->view}_id");
        $countParent = MenuName::count();

        if (!$currentParentId && $countParent) {
            $currentParent = MenuName::first();

            // Записать куку навсегда (5 лет)
            return redirect()->back()
                ->withCookie(cookie()->forever("{$this->view}_id", $currentParent->id)
                );
        }


        $parentValues = MenuName::pluck('title', 'id');
        $parentValues->prepend('Menu_names', 0);


        $f = __FUNCTION__;
        $values = null;

        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'title',
            'slug',
            'status',
            'sort',
            'id',
        ];

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;

        // Если в родительской таблице нет элементов, то ничего нельзя добавить
        if ($currentParentId) {

            // Метод для поиска и сортировки запроса БД
            $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage, 'belong_id', $currentParentId);
        }

        // Передать поля для вывода, значение l - с переводом, t - дата
        $thead = [
            'title' => 'l',
            'slug' => null,
            'status' => 'l',
            'sort' => null,
            'id' => null,
        ];

        $title = __('a.' . Str::ucfirst($this->table));
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'parentValues', 'values', 'queryArr', 'col', 'cell', 'currentParentId', 'thead'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Записать в куку id из привязанной таблице, если не записано
        $currentParentId = $request->cookie("{$this->view}_id");
        $countParent = MenuName::count();

        if (!$currentParentId && $countParent) {
            $currentParent = MenuName::first();

            // Записать куку навсегда (5 лет)
            return redirect()->back()
                ->withCookie(cookie()->forever("{$this->view}_id", $currentParent->id)
                );
        }

        $f = __FUNCTION__;
        $title = __('a.' . Str::ucfirst($f));
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'currentParentId'));
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
            'belong_id' => 'required|integer',
            'parent_id' => "nullable|integer",
            'title' => 'required|string|max:190',
        ];
        $request->validate($rules);
        $data = $request->all();

        // Создаём экземкляр модели
        $values = new Menu();

        // Заполняем модель новыми данными
        $values->fill($data);

        // Сохраняем элемент
        $values->save();

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
        // Записать в куку id из привязанной таблице, если не записано
        $currentParentId = request()->cookie("{$this->view}_id");
        $countParent = MenuName::count();

        if (!$currentParentId && $countParent) {
            $currentParent = MenuName::first();

            // Записать куку навсегда (5 лет)
            return redirect()->back()
                ->withCookie(cookie()->forever("{$this->view}_id", $currentParent->id)
                );
        }


        // Получаем элемент по id, если нет - будет ошибка
        $values = $this->model::findOrFail($id);

        // Массив всех элементов, где ключи id, а значения title
        $all = $this->model::pluck('title', 'id');
        $all->prepend('parent', 0);


        // Элементы связанные
        $valuesBelong = $values->parents;

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'all', 'valuesBelong', 'currentParentId'));
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

        $rules = [
            'belong_id' => 'required|integer',
            'parent_id' => "nullable|integer",
            'title' => 'required|string|max:190',
        ];
        $request->validate($rules);
        $data = $request->all();

        // parent_id не должны быть равно id
        if ($values->parent_id == $values->id) {
            $values->parent_id = '0';
        }

        // Если нет сортировки, то по-умолчанию 500
        $data['sort'] = empty($data['sort']) ? 500 : $data['sort'];

        // Заполняем модель новыми данными
        $values->fill($data);

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

        // Если есть потомки, то ошибка
        if ($values->parents->isNotEmpty()) {
            return redirect()
                ->route("admin.{$this->route}.edit", $id)
                ->with('error', __('s.remove_not_possible') . ', ' . __('s.there_are_nested') . __('a.id'));
        }

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
