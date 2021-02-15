<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\DbSort;
use App\Helpers\Admin\Img;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ProductGallery;

class ProductGalleryController extends AppController
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

        $this->belongTable = config('shop.product_table');

        view()->share(compact('class', 'c','model', 'table', 'route', 'view'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Поиск. Массив гет ключей для поиска
        $queryArr = [
            'title',
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
        $parentValues = DB::table($this->belongTable)
            ->whereNull('deleted_at')
            ->pluck('title', 'id');

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'parentValues'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $imagesExt = implode(',', config('admin.acceptedImagesExt') ?? []);

        $rules = [
            'product_id' => 'required|integer',
            'title' => 'required|string|max:250',
            'img' => "required|mimes:{$imagesExt}|max:2000",
        ];
        $request->validate($rules);
        $data = $request->all();


        if ($request->hasFile('img')) {

            // Обработка картинки
            $data['img'] = Img::upload($request, $this->class, null, config('admin.imgMaxSizeHD'));
            Img::copyWebp($data['img']);

        } else {

            // Если нет картинки
            $data['img'] = config("admin.img{$this->class}Default");
        }

        // Создаём экземкляр модели
        $values = new ProductGallery();

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

        $parentValues = DB::table($this->belongTable)
            ->whereNull('deleted_at')
            ->pluck('title', 'id');

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'parentValues'));
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

        $imagesExt = implode(',', config('admin.acceptedImagesExt') ?? []);

        $rules = [
            'product_id' => 'required|integer',
            'title' => 'required|string|max:250',
            'img' => "nullable|mimes:{$imagesExt}|max:2000",
        ];
        $request->validate($rules);
        $data = $request->all();


        if ($request->hasFile('img')) {

            // Обработка картинки
            $data['img'] = Img::upload($request, $this->class, $values->img, config('admin.imgMaxSizeHD'));
            Img::copyWebp($data['img']);

        } else {

            // Если нет картинки
            $data['img'] = $values->img;
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
        $img = $values->img ?? null;


        // Удаляем элемент
        $values->delete();


        // Удалим картинку с сервера, кроме картинки по-умолчанию
        Img::deleteImg($img, config("admin.img{$this->class}Default"));


        // Удалить все кэши
        cache()->flush();

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('success', __('s.removed_successfully', ['id' => $values->id]));
    }
}
