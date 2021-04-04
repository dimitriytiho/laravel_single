<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CategoriesExport;
use App\Exports\UsersExport;
use App\Helpers\ImportExport;
use App\Imports\CategoriesImport;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use \Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends AppController
{
    private $unique;


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $view = $this->view = Str::snake($this->class);

        view()->share(compact('class', 'view'));
    }


    public function view()
    {
        $method = __FUNCTION__;

        // Массив возможных get запросов
        $queryArr = [
            'user',
        ];

        // Если включен магазин, то добавим товары и категории
        if (config('add.shop')) {
            $queryArr = array_merge($queryArr, ['product', 'category']);
        }

        // Автоматически определим по какому ключу искать
        $requestQuery = request()->query() ?: [];
        $query = $requestQuery ? key($requestQuery) : null;

        // Определим гет запрос, если его нет в $queryArr, то покажем по-умолчаниею для user
        $query = in_array($query, $queryArr) ? $query : 'user';


        // Опредилим роуты
        $routeExport = null;
        $routeImport = null;
        if (Route::has("admin.export_{$query}")) {
            $routeExport = route("admin.export_{$query}");;
        }
        if (Route::has("admin.import_{$query}")) {
            $routeImport = route("admin.import_{$query}");;
        }

        $title = __("a.{$this->view}");
        return view("{$this->viewPath}.{$this->view}.{$method}", compact('title', 'queryArr', 'query', 'routeExport', 'routeImport'));
    }


    // User
    public function exportUser()
    {
        // Сохранить файл в /storage/app/import_export/products.xlsx
        /*Excel::store(new ProductsExport, 'import_export/products.xlsx');
        return back()->with('success', __('a.upload_success'));*/

        // Скачать файл
        return Excel::download(new UsersExport(), 'users.xlsx');
    }


    // Product
    public function exportProduct()
    {
        // Сохранить файл в /storage/app/import_export/products.xlsx
        /*Excel::store(new ProductsExport, 'import_export/products.xlsx');
        return back()->with('success', __('a.upload_success'));*/

        // Скачать файл
        return Excel::download(new ProductsExport(), 'products.xlsx');
    }


    public function importProduct(Request $request)
    {
        // Настройки
        $table = 'products';
        $export = new ProductsExport();
        $unique = $export->unique;
        $arrColumns = $export->columns;


        // Валидация файла, должен быть xls или xlsx
        $request->validate([
            'import_file' => 'required|mimes:xls,xlsx',
        ]);

        //$data = Excel::toArray(new ProductsImport(), request()->file('import_file'));
        $data = Excel::toCollection(new ProductsImport(), request()->file('import_file'));

        // Получаем пустой массив из 256 элементов
        $arrEmpty = [];
        for ($column = 0; $column < 256; $column++) {
            $arrEmpty[$column] = null;
        }

        if ($data->count() > 0) {
            foreach ($data->toArray() as $arrValue) {

                // Удаляем верхний ряд с наименованиями
                unset($arrValue[0]);

                if ($arrValue) {
                    $validateRow = '';
                    $insertData = [];
                    $uploadData = [];

                    foreach ($arrValue as $key => $row) {

                        // Если массив пуст, то пропустим его
                        if (!array_diff($row, $arrEmpty)) continue;

                        // Формируем данные
                        $values = ImportExport::arrColumnsRequired($row, $arrColumns, true, 'slug', 1);

                        // Проверяем уникальную колонку в таблице на уникальность
                        if (!empty($unique) && isset($values[$unique])) {
                            $uniqueCount = DB::table($table)
                                ->where($unique, $values[$unique])
                                ->where('id', '<>', $values['id'])
                                ->count();

                            if ($uniqueCount) {
                                return back()->withErrors( __('a.not_unique_element', ['id' => $values['id']]) . $unique);
                            }
                        }

                        if (is_string($values)) {

                            // Если строка, то выведем её как ошибку
                            $validateRow .= "{$values}, ";

                        } else {

                            // Если есть id, то обновим ряд
                            if ($values['id']) {
                                $id = $values['id'];
                                unset($values['id']);
                                $uploadData[$id] = $values;

                                // Если нет id, то вставим ряд
                            } else {

                                unset($values['id']);
                                $insertData[] = $values;
                            }
                        }
                    }
                }
            }
        }

        // Обновляем ряды в БД
        $messageUpdate = null;
        if (!empty($uploadData)) {

            foreach ($uploadData as $id => $values) {
                DB::table($table)
                    ->where('id', $id)
                    ->update($values);
            }

            $messageUpdate = __('a.updated_elements') . count($uploadData);
        }

        // Вставляем ряды в БД
        $messageInsert = null;
        if (!empty($insertData)) {

            DB::table($table)->insert($insertData);
            $messageInsert = __('a.new_elements_inserted') . count($insertData);
        }

        //Excel::import(new ProductsImport, request()->file('import_file'));

        // Сообщение ошибки, если были пропущены ряды, в которых не заполнены обязательные поля
        if ($validateRow) {
            $validateRow = rtrim($validateRow, ', ');
            session()->flash('error', __('a.rows_were_skipped') . $validateRow);
        }
        return back()->with('success', __('a.upload_success') . $messageUpdate . $messageInsert);
    }


    // Category
    public function exportCategory()
    {
        // Скачать файл
        return Excel::download(new CategoriesExport(), 'categories.xlsx');
    }


    public function importCategory(Request $request)
    {
        // Настройки
        $table = 'categories';
        $export = new CategoriesExport();
        $unique = $export->unique;
        $arrColumns = $export->columns;


        // Валидация файла, должен быть xls или xlsx
        $request->validate([
            'import_file' => 'required|mimes:xls,xlsx',
        ]);

        $data = Excel::toCollection(new CategoriesImport(), request()->file('import_file'));

        // Получаем пустой массив из 256 элементов
        $arrEmpty = [];
        for ($column = 0; $column < 256; $column++) {
            $arrEmpty[$column] = null;
        }

        if ($data->count() > 0) {
            foreach ($data->toArray() as $arrValue) {

                // Удаляем верхний ряд с наименованиями
                unset($arrValue[0]);

                if ($arrValue) {
                    $validateRow = '';
                    $insertData = [];
                    $uploadData = [];

                    foreach ($arrValue as $key => $row) {

                        // Если массив пуст, то пропустим его
                        if (!array_diff($row, $arrEmpty)) continue;

                        // Формируем данные
                        $values = ImportExport::arrColumnsRequired($row, $arrColumns);

                        // Проверяем уникальную колонку в таблице на уникальность
                        if (!empty($unique) && isset($values[$unique])) {
                            $uniqueCount = DB::table($table)
                                ->where($unique, $values[$unique])
                                ->where('id', '<>', $values['id'])
                                ->count();

                            if ($uniqueCount) {
                                return back()->withErrors( __('a.not_unique_element', ['id' => $values['id']]) . $unique);
                            }
                        }

                        if (is_string($values)) {

                            // Если строка, то выведем её как ошибку
                            $validateRow .= "{$values}, ";

                        } else {

                            // Если есть id, то обновим ряд
                            if ($values['id']) {
                                $id = $values['id'];
                                unset($values['id']);
                                $uploadData[$id] = $values;

                                // Если нет id, то вставим ряд
                            } else {

                                unset($values['id']);
                                $insertData[] = $values;
                            }
                        }
                    }
                }
            }
        }

        // Обновляем ряды в БД
        $messageUpdate = null;
        if (!empty($uploadData)) {

            foreach ($uploadData as $id => $values) {
                DB::table($table)
                    ->where('id', $id)
                    ->update($values);
            }

            $messageUpdate = __('a.updated_elements') . count($uploadData);
        }

        // Вставляем ряды в БД
        $messageInsert = null;
        if (!empty($insertData)) {

            DB::table($table)->insert($insertData);
            $messageInsert = __('a.new_elements_inserted') . count($insertData);
        }

        // Сообщение ошибки, если были пропущены ряды, в которых не заполнены обязательные поля
        if ($validateRow) {
            $validateRow = rtrim($validateRow, ', ');
            session()->flash('error', __('a.rows_were_skipped') . $validateRow);
        }
        return back()->with('success', __('a.upload_success') . $messageUpdate . $messageInsert);
    }
}
