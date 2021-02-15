<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\DbSort;
use App\Helpers\Admin\Img;
use App\Helpers\Date;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PromoController extends AppController
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

        // Связанные таблицы, а также в моделе должен быть метод с название таблицы, реализующий связь. Многие ко многим.
        $productsTable = config('shop.product_table');
        $relatedTables = $this->relatedTables = [

            // Товары
            $productsTable,
        ];

        view()->share(compact('class', 'c','model', 'table', 'route', 'view', 'relatedTables'));
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
            'slug',
            'status',
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
            'img' => 'img',
            'title' => null,
            'slug' => null,
            'status' => 'l',
            'sort' => null,
            'id' => null,
        ];


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
            'title' => 'required|string|max:250',
            'slug' => "required|string|unique:{$this->table}|max:250",
        ];
        $request->validate($rules);
        $data = $request->all();

        if ($request->hasFile('img')) {

            // Обработка картинки
            $data['img'] = Img::upload($request, $this->class);
            Img::copyWebp($data['img']);

        } else {

            // Если нет картинки
            $data['img'] = config("admin.img{$this->class}Default");
        }

        // Если нет body, то ''
        if (empty($data['body'])) {
            $data['body'] = '';
        }

        // Создаём экземкляр модели
        $values = new Promo();

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

        // Получаем данные связанных таблиц
        $related = [];
        if (!empty($this->relatedTables)) {
            foreach ($this->relatedTables as $relatedTable) {
                if (Schema::hasTable($relatedTable)) {
                    $related[$relatedTable] = DB::table($relatedTable)
                        ->whereNull('deleted_at')
                        ->pluck('title', 'id');
                }
            }
        }

        // Формируем даты в нужном формате
        if ($values->start) {
            $format = config('admin.date_format');
            $values->start = d($values->start, $format) . ' - ' . d($values->end, $format);
        }

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'related'));
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
            'title' => 'required|string|max:250',
            'slug' => "required|string|unique:{$this->table},slug,{$id}|max:250",
        ];
        $request->validate($rules);
        $data = $request->all();

        if ($request->hasFile('img')) {

            // Обработка картинки
            $data['img'] = Img::upload($request, $this->class, $values->img);
            Img::copyWebp($data['img']);

        } else {

            // Если нет картинки
            $data['img'] = $values->img;
        }

        // Записываем дату в нужном формате
        if (!empty($data['start'])) {
            $date = explode(' - ', $data['start']);
            $data['start'] = empty($date[0]) ? null : Date::toTimestamp($date[0]);
            $data['end'] = empty($date[1]) ? null : Date::toTimestamp($date[1]);
        }

        // Если нет body, то ''
        if (empty($data['body'])) {
            $data['body'] = '';
        }


        // Сохраняем связи
        if (!empty($this->relatedTables)) {
            foreach ($this->relatedTables as $relatedTable) {
                $values->$relatedTable()->sync($request->$relatedTable);
            }
        }


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

        // Удалить картинку, кроме картинки по-умолчанию
        Img::deleteImg($values->img, config("admin.img{$this->class}Default"));


        // Удаляем связанные элементы
        if (!empty($this->relatedTables)) {
            foreach ($this->relatedTables as $relatedTable) {
                $values->$relatedTable()->sync([]);
            }
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
