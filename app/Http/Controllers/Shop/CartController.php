<?php

namespace App\Http\Controllers\Shop;

use App\Helpers\Date;
use App\Helpers\Obj;
use App\Models\{Product, ModifierGroup, Main, Cart};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
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
        View::share(compact('class', 'c', 'model', 'route', 'view', 'table'));
    }



    // Страница оформления заказа /cart
    public function index(Request $request)
    {
        //session()->forget('cart');
        //$cartSession = session()->has('cart') ? session()->get('cart') : [];
        //dump($cartSession);


        // Вне режима работы выводим сообщение
        $modeError = !Date::timeComparison(Main::site('mode_open'), Main::site('mode_close'));
        if ($modeError) {
            session()->flash('error', Main::site('mode_message') . Main::site('mode_open') . ' - ' . Main::site('mode_close'));
        }
        /*$modeError = date('G') < Main::site('mode_open') || date('G') > Main::site('mode_open');
        if ($modeError) {
            session()->flash('error', Main::site('mode_message') . Main::site('mode_open') . ':00-' . Main::site('mode_close') . ':00');
        }*/


        $cartSession = session()->has('cart') ? session()->get('cart') : [];
        $noBtnModal = true;

        //dump(session()->get('cart'));
        //session()->forget('cart'); // Удалить сессию cart

        $title = __('s.cart');
        return view("{$this->viewPath}.{$this->view}_index", compact('title', 'cartSession', 'noBtnModal', 'modeError'));
    }



    // При запросе показывает корзину в модальном окне
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $cartSession = session()->has('cart') ? session()->get('cart') : [];
            return view("{$this->viewPath}.{$this->view}_modal")->with(compact('cartSession'))->render();
        }
        Main::getError("{$this->class} request", __METHOD__);
    }



    // При запросе добавляет товар в корзину и показывает в модальном окне
    public function plus(Request $request, $cartKey)
    {
        $cartKey = (int)$cartKey;

        // Вся корзина из сессии
        $cartSession = session()->has('cart') ? session()->get('cart') : [];

        // Получаем товар из корзины
        $product = $cartSession['products'][$cartKey] ?? [];

        // Если нет товара
        if (!$product) {
            return back()->with('error', 'Товар не найден...');
        }

        Cart::plus($cartKey);

        // Если запрос ajax
        if ($request->ajax()) {
            return view("{$this->viewPath}.{$this->view}_modal")->with(compact('product', 'cartSession'))->render();
        }
        return back(); // ->with('success', __('s.success_plus'))
    }



    // При запросе уменьшает кол-во товаров в корзине и показывает в модальном окне
    public function minus(Request $request, $cartKey)
    {
        $cartKey = (int)$cartKey;

        // Вся корзина из сессии
        $cartSession = session()->has('cart') ? session()->get('cart') : [];

        // Получаем товар
        $product = $cartSession['products'][$cartKey] ?? [];

        // Если нет товара
        if (!$product) {
            return back()->with('error', 'Товар не найден...');
        }

        Cart::minus($cartKey);

        // Если запрос ajax
        if ($request->ajax()) {
            return view("{$this->viewPath}.{$this->view}_modal")->with(compact('product', 'cartSession'))->render(); //->with(compact('product'))
        }
        return back(); // ->with('success', __('s.success_minus'))
    }



    // При запросе удаляет товар из корзины и показывает в модальном окне
    public function destroy(Request $request, $cartKey)
    {
        $cartKey = (int)$cartKey;

        // Вся корзина из сессии
        $cartSession = session()->has('cart') ? session()->get('cart') : [];

        // Получаем товар
        $product = $cartSession['products'][$cartKey] ?? [];

        // Если нет товара
        if (!$product) {
            return back()->with('error', 'Товар не найден...');
        }

        Cart::destroy($cartKey);

        // Если запрос ajax
        if ($request->ajax()) {
            return view("{$this->viewPath}.{$this->view}_modal")->with(compact('product', 'cartSession'))->render();
        }
        return back(); // ->with('success', __("{$this->lang}::s.success_destroy"))
    }


    // Проверить есть ли в корзине товар и вернёт его
    public function productInCart(Request $request, $id)
    {
        if ($request->ajax()) {

            // Товар из БД
            $product = Product::with(['modifier_groups' => function ($query) {
                $query->orderBy('sort');
            }])->find($id);

            /*$p = Product::with(['modifier_groups' => function ($query) {
            $query->orderBy('sort');
        }])
            ->with('labels')
            ->find(2);
        dd($p);*/


            // Получаем все модификаторы (группы и элементы)
            $modifiers = Obj::getBelong(ModifierGroup::class, 'modifiers');

            return view("{$this->viewPath}.{$this->view}_modal_add_to_cart", compact('product', 'modifiers'))->render();
        }
        Main::getError("{$this->class} request", __METHOD__);
    }



    public function productInCartAction(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            // Валидация
            $rules = [
                'id' => 'required|integer|max:190',
            ];
            $this->validate($request, $rules);

            // Нужные данные
            $productId = $data['id'] ?? null;
            $message = $data['message'] ?? null;
            $qty = empty((int)$data['qty']) ? 1 : (int)$data['qty'];

            // Удаляем из массива лишнее
            unset($data['_token']);
            unset($data['id']);
            unset($data['message']);
            unset($data['qty']);

            // Товар
            $product = DB::table($this->table)->find($productId);

            // Сохраняем комментарий пользователя
            $product->message = $message;

            // Добавляем в корзину
            Cart::add($product, $qty, $data);

            // Запишем в сессию, чтобы показать в модальном окне продолжить или оформить заказ
            session()->put('modal_offer', 1);

            return back();
        }
        Main::getError("{$this->class} request", __METHOD__);
    }
}
