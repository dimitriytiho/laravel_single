<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\DbSort;
use App\Models\MenuName;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuNameController extends AppController
{
    private $belongsTable = 'menu';
    private $belongsController = 'Menu';
    private $belongsView;


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $belongsTable = $this->belongsTable;
        $this->belongsView = Str::snake($this->belongsController);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\" . $this->class;
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);

        view()->share(compact('class', 'c','model', 'table', 'route', 'view', 'belongsTable'));
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
            'sort',
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
            'sort' => null,
            'id' => null,
        ];

        // Не показываем кнопки удаления
        //$deleteBtn = true;

        $f = __FUNCTION__;
        $title = __('a.' . Str::ucfirst($this->table));
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
        $title = __('a.' . Str::ucfirst($f));
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
            'title' => "required|string|unique:{$this->table},title|max:64",
        ];
        $request->validate($rules);
        $data = $request->all();

        // Создаём экземкляр модели
        $values = new MenuName();

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
        $values = $this->model::findOrFail($id);


        // Элементы связанные
        $valuesBelong = $values->menu;
        $routeBelong = $this->belongsView;

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'valuesBelong', 'routeBelong'));
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
            'title' => "required|string|unique:{$this->table},title,{$id}|max:64",
        ];
        $request->validate($rules);
        $data = $request->all();

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
        if ($values->menu && $values->menu->count()) {
            return redirect()
                ->route("admin.{$this->route}.edit", $id)
                ->with('error', __('s.remove_not_possible') . ', ' . __('s.there_are_nested') . __('a.id'));
        }

        // Удаляем элемент
        $values->delete();

        // Удалить все кэши
        cache()->flush();

        // Сообщение об успехе
        session()->flash('success', __('s.removed_successfully', ['id' => $values->id]));

        // Если удаляется id, который записан в куку, то перезапишем в куку id другого меню
        $cookie = request()->cookie("{$this->belongsView}_id");
        if ($cookie == $id) {
            $newCookie = $this->model::first();

            if ($newCookie) {
                return redirect()->route("admin.{$this->route}.index")
                    ->withCookie(cookie()->forever("{$this->belongsView}_id", $newCookie->id));
            }
        }

        return redirect()->route("admin.{$this->route}.index");
    }
}
