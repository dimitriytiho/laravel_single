<?php


namespace App\Http\Traits;

use App\Helpers\Admin\{DbSort, Img};
use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait TAdminBaseController
{
    /*
     * Перед наследование этого Trait переопеделите переменные указав нужное:
     *
     * protected $queryArr = ['title', 'id']; // Массив гет ключей для поиска
     * protected $thead = ['title' => null, 'id' => null]; // Передать поля для вывода в index виде, значения: null, l - с переводом, t - дата
     *
     * protected $validateStore = []; // Правила валидации для метода Store, если используется название таблице, вместо используйте маркер: $this->table
     * protected $validateUpdate = []; // Правила валидации для метода Update, если используется $id элемента (разрешения картинок $imagesExt), вместо используйте маркер: $id ($imagesExt)
     *
     * protected $belongTable = 'menu_groups'; // Указать связанной название таблицы
     * protected $relatedTables = ['categories', 'labels']; // Указать название таблиц
     * protected $relatedDelete = ['products']; // Указать название таблиц
     *
     *
     * Наследуйте этот Trait: use TAdminBaseController;
     * Укажите namespace: use App\Http\Traits\TAdminBaseController;
     */


    public function __construct(Request $request) {

        parent::__construct($request);

        // Название класса без окончания Controller
        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));

        // Название класса с маленькой буквы
        $c = $this->c = Str::lower($this->class);

        // Название модели
        $model = $this->model = "{$this->namespaceModels}\\" . $this->class;

        // Название таблицы
        $table = $this->table = with(new $model)->getTable();

        // Название маршрута, получем из URL
        $route = $this->route = $request->segment(2);

        // Название вида
        $view = $this->view = Str::snake($this->class);


        // Связанная таблица, должен быть метод в моделе с названием таблицы
        $belongTable = $this->belongTable;

        // Связанный маршрут
        $belongRoute = $this->belongRoute;

        // Связанные таблицы, а также в моделе должен быть метод с название таблицы, реализующий связь. Многие ко многим.
        $relatedTables = $this->relatedTables;

        // Связанные таблицы, которые нельзя удалить, если есть связанные элементы, а также в моделе должен быть метод с название таблицы, реализующий связь
        $relatedDelete = $this->relatedDelete;

        // Размер картинки, по-умолчанию из config/admin.php imgMaxSizeSM
        $this->imgSize = config("admin.{$this->imgSize}") ?: config('admin.imgMaxSizeSM');


        // Передаём в вид данные
        view()->share(compact('class', 'c','model', 'table', 'route', 'view', 'belongTable', 'belongRoute', 'relatedTables', 'relatedDelete'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Если есть связанная таблица, то запишем куку
        $parentValues = null;
        $currentParent = null;
        if ($this->belongChildren) {

            // Получаем родителя из куки
            $currentParentId = request()->cookie("{$this->table}_id");
            if ($currentParentId) {
                $currentParent = DB::table($this->belongTable)->find($currentParentId);

            } else {

                // Записать в куку id из привязанной таблице, если не записано
                $currentParent = DB::table($this->belongTable)->first();

                // Если нет родительский элементов, то предлагаем создать их
                if (!$currentParent) {
                    return redirect()
                        ->route("admin.{$this->belongRoute}.create")
                        ->with('info', __('a.create_parent_element'));
                }

                // Записать куку навсегда (5 лет)
                return redirect()
                    ->route("admin.{$this->route}.index")
                    ->withCookie(cookie()->forever("{$this->table}_id", $currentParent->id)
                    );
            }


            // Из связанной таблицы получаем объект, где id ключи, title значения
            $parentValues = DB::table($this->belongTable)->pluck('title', 'id');

            // Добавляем 0 ключ в объект - название связанной таблицы
            $parentValues->prepend($this->belongTable, 0);
        }



        // Поиск. Массив гет ключей для поиска
        $queryArr = $this->queryArr;

        // Параметры Get запроса
        $get = request()->query();
        $col = $get['col'] ?? null;
        $cell = $get['cell'] ?? null;


        // Если есть связанная таблица и в ней нет элементов, то обычный поиск
        if (!empty($currentParentId)) {

            // Метод для поиска и сортировки запроса БД
            $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage, $this->belongColumn, $currentParentId);

        } else {

            // Метод для поиска и сортировки запроса БД
            $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage);
        }

        // Передать поля для вывода, значения: null, l - с переводом, t - дата
        $thead = $this->thead;

        // Название метода
        $f = __FUNCTION__;

        // Заголовок для вида
        $title = __("a.{$this->table}");

        // Передаём данные в вид
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'values', 'queryArr', 'col', 'cell', 'thead', 'parentValues', 'currentParent'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Если есть связанная таблица, то запишем куку
        $currentParent = null;
        if ($this->belongTable) {

            // Получаем родителя из куки
            $currentParentId = request()->cookie("{$this->table}_id");
            if ($currentParentId) {
                $currentParent = DB::table($this->belongTable)->find($currentParentId);

            } else {

                // Записать в куку id из привязанной таблице, если не записано
                $currentParent = DB::table($this->belongTable)->first();

                // Если нет родительский элементов, то предлагаем создать их
                if (!$currentParent) {
                    return redirect()
                        ->route("admin.{$this->belongRoute}.create")
                        ->with('info', __('a.create_parent_element'));
                }

                // Записать куку навсегда (5 лет)
                return redirect()
                    ->route("admin.{$this->route}.index")
                    ->withCookie(
                        cookie()->forever("{$this->table}_id", $currentParent->id)
                    );
            }
        }


        // Данные из связанной таблицы
        $parentValues = null;
        if ($this->belongTable) {
            $parentValues = DB::table($this->belongTable)
                ->whereNull('deleted_at')
                ->pluck('title', 'id');
        }

        // Название метода
        $f = __FUNCTION__;

        // Заголовок для вида
        $title = __("a.{$f}");

        // Передаём данные в вид
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'currentParent', 'parentValues'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // В массиве валидации меняем строку на текущии данные
        if ($this->validateStore) {

            // Разрешённые разрешения картинок
            $imagesExt = implode(',', config('admin.acceptedImagesExt') ?? []);

            foreach ($this->validateStore as $key => $value) {
                $this->validateStore[$key] = str_replace(['$this->table', '$imagesExt'], [$this->table, $imagesExt], $value);
            }
        }

        // Валидация данных
        $request->validate($this->validateStore);

        // Сохраняем полученные данные
        $data = $request->all();


        // Картинка, в config/admin.php заполнить данные картинки
        if (config("admin.img{$this->class}Default")) {
            if ($request->hasFile('img')) {

                // Обработка картинки
                $data['img'] = Img::upload($request, $this->class, null, $this->imgSize);
                Img::copyWebp($data['img']);

            } else {

                // Если нет картинки
                $data['img'] = config("admin.img{$this->class}Default");
            }
        }

        // Чексбоксы в таблице
        if ($this->checkboxInTable) {
            foreach ($this->checkboxInTable as $checkbox) {
                $data[$checkbox] = empty($data[$checkbox]) ? '0' : '1'; // Сохранить чекбокс как 1
            }
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Получаем элемент из базы данных по id
        $values = $this->model::findOrFail($id);

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'values'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Если есть связанная таблица, то запишем куку
        $currentParent = null;
        if ($this->belongTable) {

            // Получаем родителя из куки
            $currentParentId = request()->cookie("{$this->table}_id");
            if ($currentParentId) {
                $currentParent = DB::table($this->belongTable)->find($currentParentId);

            } else {

                // Записать в куку id из привязанной таблице, если не записано
                $currentParent = DB::table($this->belongTable)->first();

                // Записать куку навсегда (5 лет)
                return redirect()
                    ->route("admin.{$this->route}.index")
                    ->withCookie(
                        cookie()->forever("{$this->table}_id", $currentParent->id)
                    );
            }
        }


        // Получаем элемент из базы данных по id
        $values = $this->model::findOrFail($id);

        // Если в элементе есть колонка parent_id
        $all = null;
        $valuesBelong = null;
        if ($values->parent_id) {

            // Записать в реестр parent_id, для построения дерева
            Main::set('parent_id', $values->parent_id);

            // Получаем все элементы в массив, где ключи id
            $all = $this->model::all()->keyBy('id')->toArray();

            // Элементы связанные
            $valuesBelong = $values->{$this->table};
        }


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

        // Название метода
        $f = __FUNCTION__;

        // Заголовок для вида
        $title = __("a.{$f}");

        // Передаём данные в вид
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'values', 'all', 'valuesBelong', 'currentParent', 'related'));
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

        // В массиве валидации меняем строку на текущии данные
        if ($this->validateUpdate) {

            // Разрешённые разрешения картинок
            $imagesExt = implode(',', config('admin.acceptedImagesExt') ?? []);

            foreach ($this->validateUpdate as $key => $value) {
                $this->validateUpdate[$key] = str_replace(['$this->table', '$id', '$imagesExt'], [$this->table, $id, $imagesExt], $value);
            }
        }

        // Если в элементе есть колонка parent_id и parent_id не должны быть равно id
        if ($values->parent_id && $values->parent_id == $values->id) {
            $values->parent_id = '0';
        }

        // Валидация данных
        $request->validate($this->validateUpdate);

        // Сохраняем полученные данные
        $data = $request->all();

        // Картинка, в config/admin.php заполнить данные картинки
        if (config("admin.img{$this->class}Default")) {
            if ($request->hasFile('img')) {

                // Обработка картинки
                $data['img'] = Img::upload($request, $this->class, $values->img, null, $this->imgSize);
                Img::copyWebp($data['img']);

            } else {

                // Если нет картинки
                $data['img'] = $values->img;
            }
        }

        // Сохраняем связи
        if (!empty($this->relatedTables)) {
            foreach ($this->relatedTables as $relatedTable) {
                $values->$relatedTable()->sync($request->$relatedTable);
            }
        }

        // Чексбоксы в таблице
        if ($this->checkboxInTable) {
            foreach ($this->checkboxInTable as $checkbox) {
                $data[$checkbox] = empty($data[$checkbox]) ? '0' : '1'; // Сохранить чекбокс как 1
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
        // Получаем элемент из базы данных по id, если нет - будет ошибка
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

        // Если в элементе есть колонка parent_id и есть потомки, то ошибка
        if ($values->parents && $values->parents->isNotEmpty()) {
            return redirect()
                ->route("admin.{$this->route}.edit", $id)
                ->withErrors(__('s.remove_not_possible') . ', ' . __('s.there_are_nested') . __('a.id'));
        }

        // Если есть связанная таблица и связанные элементы, то ошибка
        if ($this->belongDelete && $this->belongTable && $values->{$this->belongTable} && $values->{$this->belongTable}->count()) {
            return redirect()
                ->route("admin.{$this->route}.edit", $id)
                ->withErrors(__('s.remove_not_possible') . ', ' . __('s.there_are_nested') . __('a.id'));
        }


        // Удаляем связанные элементы
        if (!empty($this->relatedTables)) {
            foreach ($this->relatedTables as $relatedTable) {
                $values->$relatedTable()->sync([]);
            }
        }


        // Удалить картинку, кроме картинки по-умолчанию
        Img::deleteImg($values->img, config("admin.img{$this->class}Default"));

        // Удаляем элемент
        $values->delete();

        // Удалить все кэши
        cache()->flush();


        // Является родителем для связанного элемента
        if ($this->belongTable && !$this->belongChildren) {

            // Если удаляется id, который записан в куку, то перезапишем в куку id другого меню
            $cookie = request()->cookie("{$this->belongTable}_id");
            if ($cookie == $id) {
                $newCookie = $this->model::first();

                if ($newCookie) {
                    return redirect()
                        ->route("admin.{$this->route}.index")
                        ->withCookie(cookie()->forever("{$this->belongTable}_id", $newCookie->id));
                }
            }
        }


        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('success', __('s.removed_successfully', ['id' => $values->id]));
    }
}
