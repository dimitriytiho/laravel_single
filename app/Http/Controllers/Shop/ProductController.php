<?php

namespace App\Http\Controllers\Shop;

use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ProductController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "$this->namespaceModels\\{$this->class}";
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(1);
        $view = $this->view = Str::snake($this->c);
        Main::set('c', $c);
        View::share(compact('class', 'c','model', 'table', 'route', 'view'));
    }


    public function show($slug)
    {
        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {
            $values = $this->model::with('labels')
                ->where('slug', $slug)
                ->firstOrFail();

        } else {
            $values = $this->model::with('labels')
                ->where('slug', $slug)
                ->where('status', $this->statusActive)
                ->firstOrFail();
        }


        /*
         * Если есть подключаемые файлы (текст в контенте ##!!!inc_name, а сам файл в /resources/views/inc), то они автоматически подключатся.
         * Если нужны данные из БД, то в моделе сделать метод, в котором получить данные и вывести их, в подключаемом файле.
         * Дополнительно, в этот файл передаются данные страницы $values.
         */
        $values->body = Main::inc($values->body, $values);

        // Использовать скрипты в контенте, они будут перенесены вниз страницы.
        $values->body = Main::getDownScript($values->body);


        // Передаём в контейнер id и view элемента
        Main::set('id', $values->id);
        Main::set('view', $this->view);

        // Хлебные крошки
        $categoryId = $values->category[0]->id ?? null;
        $breadcrumbs = $this->breadcrumbs
            ->values('categories')
            ->end([
                route($this->route, $values->slug) => $values->title
            ])
            ->dynamic($categoryId, 'category')
            //->add([[route('catalog') => 'catalog']])
            ->get();

        $title = $values->title ?? null;
        $description = $values->description ?? null;
        return view("{$this->viewPath}.{$this->c}_show", compact('title', 'description', 'values', 'breadcrumbs'));
    }
}
