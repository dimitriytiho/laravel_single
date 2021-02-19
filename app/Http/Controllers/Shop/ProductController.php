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
        // Если пользователя есть разрешение к админскому классу, то будут показываться неактивные страницы
        if (checkPermission($this->class)) {

            $product = $this->model::whereSlug($slug)
                ->firstOrFail();

        } else {

            $product = $this->model::whereSlug($slug)
                ->active()
                ->firstOrFail();

            $product->savePopular; // Прибавляем популяность
        }


        /*
         * Если есть подключаемые файлы (текст в контенте ##!!!inc_name, а сам файл в /resources/views/inc), то они автоматически подключатся.
         * Если нужны данные из БД, то в моделе сделать метод, в котором получить данные и вывести их, в подключаемом файле.
         * Дополнительно, в этот файл передаются данные страницы $values.
         */
        $product->body = Main::inc($product->body, $product);

        // Использовать скрипты в контенте, они будут перенесены вниз страницы.
        $product->body = Main::getDownScript($product->body);


        // Передаём в контейнер id и view элемента
        Main::set('id', $product->id);
        Main::set('view', $this->view);

        // Хлебные крошки
        $categoryId = $product->categories[0]->id ?? null;
        $breadcrumbs = $this->breadcrumbs
            ->values('categories')
            ->end([
                route($this->route, $product->slug) => $product->title
            ])
            ->dynamic($categoryId, 'category')
            ->add([[route('catalog') => 'catalog']])
            ->get();

        $title = $product->title ?? null;
        $description = $product->description ?? null;
        return view("{$this->viewPath}.{$this->c}_show", compact('title', 'description', 'product', 'breadcrumbs'));
    }
}
