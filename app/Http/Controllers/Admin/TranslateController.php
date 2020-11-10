<?php

namespace App\Http\Controllers\Admin;

use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\File as helpersFile;

class TranslateController extends AppController
{
    private $locale;
    private $locales;
    private $storagePath;
    private $filePath;
    private $fileTranslate;


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $route = $this->route = $request->segment(2);
        $view = $this->view = Str::snake($this->class);

        $locale = $this->locale = app()->getLocale();
        $locales = $this->locales = config('admin.locales');
        $storagePath = $this->storagePath = '/resources/lang';
        $filePath = $this->filePath = resource_path('lang');
        $fileTranslate = $this->fileTranslate = 't.php';

        view()->share(compact('class', 'c', 'route', 'view', 'locale', 'locales', 'storagePath', 'filePath', 'fileTranslate'));
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
            'id',
        ];
        $col = request()->query('col');
        $cell = request()->query('cell');

        $values = null;
        $translation = [];
        $locales = $this->locales;
        //$storage = Storage::disk('app');
        //$storageFile = "{$this->storagePath}/{$this->locale}/$this->fileTranslate";

        if (!empty($locales)) {

            // Удаляем из массива текущую локаль
            if (in_array($this->locale, $locales)) unset($locales[array_search($this->locale, $locales)]);

            foreach ($locales as $locale) {
                $file = "{$this->filePath}/{$locale}/$this->fileTranslate";

                if (File::exists(($file))) {
                    $translation[$locale] = include $file;
                }
            }
        }


        $file = "{$this->filePath}/{$this->locale}/$this->fileTranslate";
        if (File::exists(($file))) {

            $valuesObj = include $file;

            // Если есть строка поиска
            if ($col && in_array($col, $queryArr) && $cell) {
                if (!empty($valuesObj)) {
                    foreach ($valuesObj as $k => $v) {
                        if (strpos($k, $cell) !== false) {
                            $valuesObjLimit[$k] = $v;
                        }
                    }
                }
                $valuesObj = isset($valuesObjLimit) ? $valuesObjLimit : $valuesObj;
            }

            // Делаем из массива объект
            $valuesObj = $valuesObj ? array_reverse($valuesObj) : null;
            $valuesObj = collect($valuesObj);

            // Пагинация из объекта
            $currentPage = Paginator::resolveCurrentPage();
            $currentPageItems = $valuesObj->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
            $values = new Paginator($currentPageItems, count($valuesObj), $this->perPage);
            $values->setPath($request->url());
            $values->appends($request->all());
        }

        $f = __FUNCTION__;
        $title = __('a.' . Str::ucfirst($this->view));
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'values', 'translation', 'queryArr', 'col', 'cell'));
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
            'id' => 'required|max:250',
        ];

        if (!empty($this->locales)) {
            foreach ($this->locales as $locale) {
                $rules = array_merge($rules, [$locale => 'required|string|max:250']);
            }
        }

        $request->validate($rules);

        // Формируем данные
        $id = $request->id ?? null;
        if (isset($request->_token)) unset($request['_token']);
        if (isset($request->id)) unset($request['id']);
        $data = $request->all();

        foreach ($data as $locale => $translation) {
            $file = "{$this->filePath}/{$locale}/$this->fileTranslate";


            if (File::exists(($file))) {
                $oldValues = include $file;

                if (!key_exists($id, $oldValues)) {
                    $values = array_merge($oldValues, [$id => $translation]);

                    // Сохраняем данные в файл
                    helpersFile::arrayToFile($file, $values);

                } else {

                    // Сообщение об ошибке: такой перевод уже есть
                    return redirect()
                        ->back()
                        ->with('error', __('s.translation_already'));
                }

            } else {
                return redirect()
                    ->back()
                    ->with('error', __('s.something_went_wrong'));
            }
        }

        // Задержка
        sleep(3);

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.edit", $id)
            ->with('success', __('s.created_successfully', ['id' => $id]));
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
        $values = [];

        if (!empty($this->locales)) {
            foreach ($this->locales as $locale) {
                $file = "{$this->filePath}/{$locale}/$this->fileTranslate";

                if (File::exists(($file))) {
                    $translation = include $file;

                    if (isset($translation[$id])) {
                        $values[$locale] = $translation[$id];
                    }
                }
            }
        }

        if (!$values) {
            return redirect()
                ->back()
                ->with('error', __('s.something_went_wrong'));
        }

        $f = __FUNCTION__;
        $title = __("a.{$f}");
        return view("{$this->viewPath}.{$this->view}.{$this->template}", compact('title', 'id', 'values'));
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
        $rules = [
            'id' => 'required|max:250',
        ];

        if (!empty($this->locales)) {
            foreach ($this->locales as $locale) {
                $rules = array_merge($rules, [$locale => 'required|string|max:250']);
            }
        }
        $request->validate($rules);

        // Формируем данные
        if (isset($request->_method)) unset($request['_method']);
        if (isset($request->_token)) unset($request['_token']);
        if (isset($request->id)) unset($request['id']);
        $data = $request->all();

        foreach ($data as $locale => $translation) {
            $file = "{$this->filePath}/{$locale}/$this->fileTranslate";

            if (File::exists(($file))) {
                $oldValues = include $file;
                if (isset($oldValues[$id])) unset($oldValues[$id]);
                $values = array_merge($oldValues, [$id => $translation]);

                // Сохраняем данные в файл
                helpersFile::arrayToFile($file, $values);
            }
        }

        // Задержка
        sleep(3);

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.edit", $id)
            ->with('success', __('s.saved_successfully', ['id' => $id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        foreach ($this->locales as $locale) {
            $file = "{$this->filePath}/{$locale}/$this->fileTranslate";

            if (File::exists(($file))) {
                $values = include $file;
                if (isset($values[$id])) unset($values[$id]);

                // Сохраняем данные в файл
                helpersFile::arrayToFile($file, $values);
            }
        }

        // Задержка
        sleep(3);

        // Сообщение об успехе
        return redirect()
            ->route("admin.{$this->route}.index")
            ->with('success', __('s.removed_successfully', ['id' => $id]));
    }
}
