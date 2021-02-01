<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends AppController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\{$class}";
        $table = $this->table = with(new $model)->getTable();
        $view = $this->view = Str::snake($this->c);

        view()->share(compact('class', 'c', 'model', 'table', 'view'));
    }

    public function index()
    {
        $categories = DB::table("user_{$this->view}")
            ->where('user_id', auth()->user()->id)
            ->orderBy('popular');


        // Соединяем таблицы и делаем сортировку по популярности
        $values = $this->model::with('products')
            ->join("user_{$this->view}", function ($join) use ($categories) {
                $join->on("{$this->table}.id", '=', "user_{$this->view}.{$this->view}_id")
                    ->whereIn("{$this->table}.id", $categories->pluck("{$this->view}_id"));
            })
            ->orderBy("user_{$this->view}.popular")
            ->paginate($this->perPage);


        $f = Str::snake(__FUNCTION__);
        $title = 'История категорий';

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->add([[route("home.{$this->c}_index") => $title]])
            ->add([[route('home.index') => 'account']])
            ->get();

        return view("{$this->viewPath}.{$this->view}_{$f}", compact('title', 'breadcrumbs', 'values'));
    }
}
