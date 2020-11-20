<?php

namespace App\Http\Controllers\Shop;

use App\Models\Main;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class CategoryController extends AppController
{
    // Кол-во товаров на странице каталог
    public $limit = 96;


    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = "{$this->namespaceModels}\\{$this->class}";
        $table = $this->table = with(new $model)->getTable();
        $route = $this->route = $request->segment(1);
        $view = $this->view = Str::snake($this->c);
        Main::set('c', $c);
        View::share(compact('class', 'c','model', 'table', 'route', 'view'));
    }


    // Страница Каталог
    public function index(Request $request)
    {
        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {

            $products = Product::limit($this->limit)
                ->paginate();

        } else {

            $products = Product::where('status', $this->statusActive)
                ->limit($this->limit)
                ->paginate();

        }

        $title = __('s.catalog');

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->end(['catalog' => $title])
            ->get();

        return view("{$this->viewPath}.{$this->view}_index", compact('title', 'products', 'breadcrumbs'));
    }


    public function show($slug, Request $request)
    {
        // Если пользователь админ, то будут показываться неактивные страницы
        if (auth()->check() && auth()->user()->Admin()) {

            $values = $this->model::with('products')
                ->where('slug', $slug)
                ->firstOrFail();

        } else {

            $values = $this->model::with('products')
                ->where('slug', $slug)
                ->where('status', $this->statusActive)
                ->firstOrFail();
        }

        $products = new Paginator($values->products, $values->products->count(), $this->perPage);


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
        $breadcrumbs = $this->breadcrumbs
            ->values($this->table)
            ->dynamic($values->id, 'category')
            //->add([[route('catalog') => 'catalog']])
            ->get();


        $title = $values->title ?? null;
        $description = $values->description ?? null;
        return view("{$this->viewPath}.{$this->view}_show", compact('title', 'description', 'values', 'products', 'breadcrumbs'));
    }


    // Получаем товары категории через Ajax
    public function getProduct(Request $request)
    {
        if ($request->ajax()) {
            $categoryId = $request->categoryId ?? null;

            if ((int)$categoryId) {
                $category = $this->model::withActiveSort('products')
                    ->where('id', $categoryId)
                    ->where('status', $this->statusActive)
                    ->first();

                //du($category->products); die;
                return view('inc.products')
                    ->with(['products' => $category->products])
                    ->render();
            }
        }
        // Сообщение что-то пошло не так
        Main::getError("{$this->class} request", __METHOD__);
    }
}
