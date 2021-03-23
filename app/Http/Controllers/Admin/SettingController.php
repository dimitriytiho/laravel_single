<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\DbSort;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends AppController
{
    private $titleNoEditArr = [];


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\" . $this->class;
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);

        $this->titleNoEditArr = Setting::titleNoEditArr() ?? [];

        view()->share(compact('class', 'c','model', 'table', 'route', 'view'));
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
            'value',
            'type',
            'section',
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
            'value' => null,
            'type' => 'l',
            'section' => null,
            'id' => null,
        ];


        // Id элементов, которые нельзя удалять
        //$guardedIds = $this->model::whereIn('title', $this->titleNoEditArr)->pluck('id')->toArray();


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
            'title' => "required|string|unique:{$this->table}|max:250",
        ];
        $request->validate($rules);
        $data = $request->all();

        // Если тип checkbox, то сохраним 1 или 0
        if (isset($data['type']) && $data['type'] === (config('admin.setting_type')[1] ?? 'checkbox')) {
            $data['value'] = empty($data['value']) ? '0' : '1';
        }

        // Создаём экземкляр модели
        $values = new $this->model();

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $values = $this->model::findOrFail($id);

        // Если title запрещён к редактированию
        $disabledDelete = in_array($values->title, $this->titleNoEditArr) ? 'readonly' : null;

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'disabledDelete'));
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
            'title' => "required|string|unique:{$this->table},title,{$id}|max:250",
        ];
        $request->validate($rules);
        $data = $request->all();

        // Если тип checkbox, то сохраним 1 или 0
        if (isset($data['type']) && $data['type'] === (config('admin.setting_type')[1] ?? 'checkbox')) {
            $data['value'] = empty($data['value']) ? '0' : '1';
        }

        // Заполняем модель новыми данными
        $values->fill($data);

        // Если title запрещён к редактированию
        if (in_array($values->title, $this->titleNoEditArr) && $values->title != $request->title) {

            // Сообщение об ошибке
            return redirect()
                ->route("admin.{$this->route}.edit", $values->id)
                ->with('error', __('s.something_went_wrong'));
        }

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

        // Если title запрещён к редактированию
        if (in_array($values->title, $this->titleNoEditArr)) {

            // Сообщение об ошибке
            return redirect()
                ->route("admin.{$this->route}.edit", $values->id)
                ->with('error', __('s.something_went_wrong'));
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
