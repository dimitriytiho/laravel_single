<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Admin\DbSort;
use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StopListController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\Product";
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
        $values = DbSort::getSearchSort($queryArr, $get, $this->table, $this->model, $this->view, $this->perPage, 'status', $this->statusActive);

        $inactive = $this->model::where('status', config('add.page_statuses')[0] ?? 'inactive')->get();

        $f = __FUNCTION__;
        $title = __('a.stop_list');
        return view("{$this->viewPath}.{$this->view}.{$f}", compact('title', 'values', 'queryArr', 'col', 'cell', 'inactive'));
    }


    public function update(Request $request)
    {
        $model = "{$this->namespaceModels}\\Product";
        $id = (int)$request->id;
        $status = $request->status;

        if ($request->ajax() && $id && $status && in_array($status, config('add.page_statuses') ?: [])) {

            $item = $model::find($id);

            // Меняем статус
            $item->status = $status;
            $item->save();

            // Удалить все кэши
            cache()->flush();

            $item = view('admin.stop_list.item', compact('item'))->render();

            if ($status === $this->statusActive) {

                // Если статус активный
                $res = 1;

            } else {

                // Если статус не активный
                $res = null;
            }

            return response()->json([
                'status' => $res,
                'item' => $item,
            ]);
        }
        Main::getError('Request No Ajax', __METHOD__);
    }
}
