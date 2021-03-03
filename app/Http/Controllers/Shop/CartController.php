<?php

namespace App\Http\Controllers\Shop;

use App\Models\{Main, Cart};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        // Таблица товаров в БД
        $table = $this->table = config('shop.product_table');

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $model = $this->model = null;
        $route = $this->route = $request->segment(1);
        $view = $this->view = Str::snake($this->c);
        Main::set('c', $c);
        view()->share(compact('class', 'c', 'model', 'route', 'view', 'table'));
    }


    // Страница оформления заказа /cart
    public function index(Request $request)
    {
        //dump(session('cart'));
        //session()->forget('cart');

        $noBtnModal = true;
        $title = __('s.cart');

        $promoMatches = $promoMatchesNoScore = Promo::matches();
        $promoData = [];
        if (!empty($promoMatches)) {

            if (isset($promoMatchesNoScore['score'])) {
                unset($promoMatchesNoScore['score']);
            }

            // Получаем акции
            $promos = Promo::active()
                ->betweenTime()
                ->whereIn('id', $promoMatchesNoScore)
                ->get()
                ->keyBy('id');


            // Расчитываем акции
            if ($promos->isNotEmpty()) {
                foreach ($promos as $id => $promo) {
                    $method = 'calc' . Str::studly($promo->type);
                    if (method_exists('\App\Models\Promo', $method)) {
                        $promoData[$id] = Promo::$method($promo);
                    }
                }
            }
        }

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->end(['cart' => $title])
            ->get();

        return view("{$this->viewPath}.{$this->view}_index", compact('title', 'breadcrumbs', 'noBtnModal', 'promoMatches', 'promoData'));
    }


    // При запросе показывает корзину в модальном окне
    public function show(Request $request)
    {
        if ($request->ajax()) {
            return view("{$this->viewPath}.{$this->view}_modal")->render();
        }
        Main::getError("{$this->class} request", __METHOD__);
    }


    // При запросе добавляет товар в корзину и показывает в модальном окне
    public function add(Request $request, int $productId)
    {
        $qty = (int)$request->qty ? (int)$request->qty : 1;

        // Товар
        $product = DB::table($this->table)
            ->whereNull('deleted_at')
            ->whereStatus($this->statusActive)
            ->find($productId);

        if (!$product) {
            return redirect()->route('catalog')->with('error', __('s.product_not_found'));
        }

        // Добавляем в корзину
        Cart::add($product, $qty);

        // Если запрос ajax
        if ($request->ajax()) {
            return view("{$this->viewPath}.{$this->view}_modal")->render();

        } else {

            session()->flash('success', __('s.success_plus'));
        }
        return back();
    }



    // При запросе прибавит товар в корзине и показывает в модальном окне
    public function plus(Request $request, int $cartKey)
    {
        // Если нет товара
        if (!session()->has("cart.products.{$cartKey}")) {
            return redirect()->route('catalog')->with('error', __('s.product_not_found'));
        }

        Cart::plus($cartKey);

        // Если запрос ajax
        if ($request->ajax()) {
            return view("{$this->viewPath}.{$this->view}_modal")->render();
        }
        return back();
    }



    // При запросе уменьшит товар в корзине и показывает в модальном окне
    public function minus(Request $request, int $cartKey)
    {
        // Если нет товара
        if (!session()->has("cart.products.{$cartKey}")) {
            return redirect()->route('catalog')->with('error', __('s.product_not_found'));
        }

        Cart::minus($cartKey);

        // Если запрос ajax
        if ($request->ajax()) {
            return view("{$this->viewPath}.{$this->view}_modal")->render();
        }
        return back();
    }



    // При запросе удаляет товар из корзины и показывает в модальном окне
    public function remove(Request $request, int $cartKey)
    {
        // Если нет товара
        if (!session()->has("cart.products.{$cartKey}")) {
            return redirect()->route('catalog')->with('error', __('s.product_not_found'));
        }

        Cart::remove($cartKey);

        // Если запрос ajax
        if ($request->ajax()) {
            return view("{$this->viewPath}.{$this->view}_modal")->render();
        }
        return back();
    }
}
