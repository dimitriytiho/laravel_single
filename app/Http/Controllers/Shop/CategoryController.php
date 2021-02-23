<?php

namespace App\Http\Controllers\Shop;

use App\Models\FilterGroup;
use App\Models\Main;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
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
        // Если пользователя есть разрешение к админскому классу, то будут показываться неактивные страницы
        if (checkPermission($this->class)) {

            $cats = $this->model::with('products')
                ->paginate($this->perPage);
        } else {

            $cats = $this->model::with('products')
                ->active()
                ->paginate($this->perPage);

        }

        // Если пользователя есть разрешение к админскому классу, то будут показываться неактивные страницы
        /*if (checkPermission('Product')) {

            $products = Product::paginate($this->perPage);
        } else {

            $products = Product::active()
                ->paginate($this->perPage);

        }*/

        $title = __('s.catalog');

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->end(['catalog' => $title])
            ->get();

        return view("{$this->viewPath}.{$this->view}_index", compact('title', 'breadcrumbs', 'cats'));
    }


    public function show($slug, Request $request)
    {
        // Get параметры
        $priceFromGet = $request->query('from');
        $priceToGet = $request->query('to');
        $filtersGet = $request->query('filter');
        $filtersArr = [];
        if ($filtersGet) {
            $filtersGet = rtrim($filtersGet, ',');
            $filtersArr = explode(',', $filtersGet);
        }

        // Получаем категорию с товарами
        $categories = $this->model::getCategoryWithProductByFilter($slug, $priceFromGet, $priceToGet, $filtersGet);



        // Принимает данные фильтра через Ajax запрос
        if ($request->ajax()) {
            $priceFrom = $request->priceFrom;
            $priceTo = $request->priceTo;
            $filters = $request->filters;

            // Получаем категорию с товарами
            $values = $this->model::getCategoryWithProductByFilter($slug, $priceFrom, $priceTo, $filters)->first();

            // Товары
            $products = new Paginator($values->products, $values->products->count(), $this->perPage);
            $col9 = true;

            // Возвщаем готовые товары
            return view('inc.products', compact('products', 'col9'))->render();
        }


        // Если пользователя есть разрешение к админскому классу
        if (!$checkPermission = checkPermission($this->class)) {

            // Для пользователей
            $categories = $categories->active(); // Будут показываться только активные страницы
        }

        // Если нет категории, то ошибка
        $categories = $categories->firstOrFail();

        if (!$checkPermission) {
            $categories->savePopular; // Прибавляем популяность
        }

        // Товары
        $products = new Paginator($categories->products, $categories->products->count(), $this->perPage);
        $minPrice = $priceFromGet ?: (int)$products->min('price');
        $maxPrice = $priceToGet ?: (int)$products->max('price');


        /*
         * Если есть подключаемые файлы (текст в контенте ##!!!inc_name, а сам файл в /resources/views/inc), то они автоматически подключатся.
         * Если нужны данные из БД, то в моделе сделать метод, в котором получить данные и вывести их, в подключаемом файле.
         * Дополнительно, в этот файл передаются данные страницы $categories.
         */
        $categories->body = Main::inc($categories->body, $categories);

        // Использовать скрипты в контенте, они будут перенесены вниз страницы.
        $categories->body = Main::getDownScript($categories->body);


        // Передаём в контейнер id и view элемента
        Main::set('id', $categories->id);
        Main::set('view', $this->view);


        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->values($this->table)
            ->dynamic($categories->id, 'category')
            ->add([[route('catalog') => 'catalog']])
            ->get();


        // В вид фильтров передаём фильтры
        view()->composer('inc.filters', function ($view) {

            // Кэшируем запрос в БД
            if (cache()->has('filter_groups_with_filters')) {
                $filterGroups = cache()->get('filter_groups_with_filters');
            } else {
                $filterGroups = FilterGroup::with('filters')->get();
                cache()->forever('filter_groups_with_filters', $filterGroups);
            }

            $view->with('filterGroups', $filterGroups);
        });


        $title = $categories->title ?? null;
        $description = $categories->description ?? null;
        return view("{$this->viewPath}.{$this->view}_show", compact('title', 'description', 'categories', 'products', 'breadcrumbs', 'minPrice', 'maxPrice', 'filtersArr'));
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


    // Записать куку сортировки товаров
    public function sort($sort, Request $request)
    {
        if (in_array($sort, config('shop.sort') ?: [])) {
            return redirect()->back()->withCookie('sort', $sort);
        }
        // Сообщение что-то пошло не так
        Main::getError("{$this->class} request", __METHOD__);
    }
}
