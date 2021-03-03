<?php

namespace App\Http\Controllers\Shop;

use App\Mail\SendMail;
use App\Models\{Coupon, Main, Order, Promo, UserAdmin};
use Illuminate\Http\Request;
use Illuminate\Support\{Carbon, Str};
use Illuminate\Support\Facades\{DB, Mail, View};
use App\Helpers\Str as HelpersStr;

class OrderController extends AppController
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
        View::share(compact('class', 'c','model', 'route', 'view', 'table'));
    }


    // Принимает post форму оформления заказа
    public function makeOrder(Request $request)
    {
        // Валидация
        $rules = [
            'name' => 'required|string|max:250',
            'tel' => "required|tel|max:250",
            'email' => 'required|string|email|max:250',
        ];

        // Для доставки добавляем адрес
        if(session()->has('delivery.delivery') && session('delivery.delivery')) {
            $rules += [
                'address' => 'required|string|max:250',
            ];
        }

        // Для не авторизированных добавляем согласие на обработку данных
        if (!auth()->check()) {
            $rules += [
                'accept' => 'accepted',
            ];
        }

        // Если есть ключ Recaptcha и не локально запущен сайт
        if (config('add.env') !== 'local' && config('add.recaptcha_public_key')) {
            $rules += [
                'g-recaptcha-response' => 'required|recaptcha',
            ];
        }
        $request->validate($rules);

        $data = $request->all();

        // Сохраним пользователя отправителя формы, получим его id
        $user = UserAdmin::saveUser($request);
        $userId = $user->id ?? null;
        if (!$userId) {
            // Сообщение об ошибке
            return redirect()->back()->with('error', __('s.whoops'));
        }

        // Данные в корзине
        $cart = session()->has('cart') ? session('cart') : [];

        // Данные для таблицы orders
        $dataOrder['user_id'] = $userId;
        $dataOrder['ip'] = $request->ip();

        if (!empty($data['message'])) {
            $dataOrder['message'] = s($data['message']);
        }

        // Сохраняем элементы корзины кроме товаров
        if ($cart) {
            foreach ($cart as $cartElement => $value) {
                if ('products' !== $cartElement) {
                    $dataOrder[$cartElement] = $value;
                }
            }
        }

        // UTM метка
        if (session()->has('utm.source')) {
            $dataOrder['user_source'] = session()->get('utm.source');
        }
        if (session()->has('utm.all')) {
            $dataOrder['user_utm'] = session()->get('utm.all');
        }


        // Акции
        if (session()->has('promo.score')) {
            $dataOrder['discount_score'] = session('promo.score');

            // Уменьшить у пользователя счёт
            $user->score = $user->score - $dataOrder['discount_score'];
            $user->update();
        }
        if (session()->has('promo.coupon_id')) {
            $dataOrder['coupon_id'] = session('promo.coupon_id');

            // Сделать купон не автивным
            $coupon = Coupon::find($dataOrder['coupon_id']);
            if ($coupon) {
                $coupon->status = config('add.page_statuses')[0] ?? 'inactive';
                $coupon->update();
            }
        }
        if (session()->has('promo.promo_id')) {
            $dataOrder['promo_id'] = session('promo.promo_id');
        }
        if (session()->has('promo.percent')) {
            $dataOrder['discount_percent'] = session('promo.percent');
        }
        if (session()->has('promo.sum')) {
            $dataOrder['discount_sum'] = session('promo.sum');
        }


        // Если приходит доставка, то прибавим её к сумме, т.к. она подставляется через JS
        if (session()->has('delivery.title')) {
            $dataOrder['delivery'] = session('delivery.title');
        }
        if (session()->has('delivery.sum')) {
            $dataOrder['delivery_sum'] = session('delivery.sum');
            $dataOrder['sum'] = ($dataOrder['sum'] ?? 0) + $dataOrder['delivery_sum'];
        }

        $order = new Order();
        $order->fill($dataOrder);

        //$method = Str::kebab(__FUNCTION__); // Из contactUs будет contact-us
        if ($order->save()) {

            // Удалим сессию utm
            session()->forget('utm');

            $orderId = $order->id;
            $data['date'] = d(time(), config('admin.date_format') ?: 'dd.MM.y HH:mm');


            // Данные для таблицы order_products
            if (!empty($cart['products'])) {
                $i = 0;
                foreach ($cart['products'] as $key => $product) {

                    // Товары
                    $products[$i]['order_id'] = $orderId;
                    $products[$i]['product_id'] = $product->id;
                    $products[$i]['message'] = !empty($product->message) ? $product->message : null;
                    $products[$i]['discount'] = !empty($product->discount) ? $product->discount : null;
                    $products[$i]['qty'] = (int)$product->qty;
                    $products[$i]['price'] = (float)$product->price;
                    $products[$i]['created_at'] = $products[$i]['updated_at'] = Carbon::now();

                    // Модификаторы
                    /*if (!empty($product->modifiers) && is_array($product->modifiers)) {
                        $products[$i]['modifiers'] = serialize($product->modifiers);
                    } else {
                        $products[$i]['modifiers'] = null;
                    }*/
                    $i++;
                }

                // Вставим товары в таблицу
                if (!empty($products)) {
                    DB::table('order_product')->insert($products);
                }
            }


            // Очистим корзину, удалив сессию
            session()->forget('cart');


            // Письмо пользователю
            try {
                $title = __('s.You_placed_order') . config('add.domain');
                $body = __('s.Your_order_was_successfully_received');

                // Отправить письмо
                Mail::to($user->email)
                    ->send(new SendMail($title, $body, $cart, $this->c));

            } catch (\Exception $e) {
                Main::getError("Error sending email User: $e", __METHOD__, false);
            }

            // Письмо администратору
            try {
                $title = __('s.An_order_has_been_placed', ['order_id' => $orderId]) . config('add.domain');
                $email_admin = HelpersStr::strToArr(Main::site('admin_email') ?? null);

                // Данные заказа
                if (!empty($dataUserMail) && view()->exists("{$this->viewPath}.mail.table_form")) {
                    $bodyOrder = view("{$this->viewPath}.mail.table_form")
                        ->with(['values' => $dataUserMail])
                        ->render();
                }

                // Данные о товарах
                if (!empty($cart) && view()->exists('mail.cart')) {
                    $bodyProducts = view('mail.cart')
                        ->with(['values' => $cart])
                        ->with(['delivery' => $dataOrder['delivery_sum'] ?? '0'])
                        ->render();
                }

                $body = ($bodyOrder ?? null) . "<br><br><br>" . ($bodyProducts ?? null);

                // Отправить письмо
                Mail::to($email_admin)->
                send(new SendMail($title, $body, $cart, $this->c));

            } catch (\Exception $e) {
                Main::getError("Error sending email Admin: {$e}", __METHOD__, false);
            }



            // ЕСЛИ ПОЛЬЗОВАТЕЛЬ ВЫБРАЛ ОПЛАТУ ОНЛАЙН
            /*if (
                !empty($data['payment'])
                && !empty(config('shop.payment')[3]['title'])
                && config('shop.payment')[3]['title'] === $data['payment']
            ) {

                $url = config('add.url');
                $resPayment = Order::getPaymentSberbank($order);

                // Если банк передал Url
                if (!empty($resPayment['url']) && $resPayment['url'] !== $url) {

                    // Редирект на страницу банка для оплаты
                    return redirect($resPayment['url']);
                }

                // В любых других случаях ошибка
                if (!empty($resPayment['error'])) {

                    // Возникла ошибка на стороне банка, то покажем её
                    session()->put('error', $resPayment['error']);
                }

                return redirect()->route('error_payment');
            }*/


            // Сообщение об успехе
            return redirect()->route('success_page')->with('success', __('s.order_successfully'));
        }
    }


    /*

     Скидки
     'promo' = [
        'title',
        'promo_id',
        'coupon_id',
        'score', // Счёт пользователя
        'percent', // Процент скидки
        'sum', // Сумма скидки
        'products_id', // Подарочные товары
        'qty', // Кол-во подарочных товаров
    ];

     */
    public function promo(Request $request)
    {
        //dd($request->all());
        $promo = $request->promo;
        //$promo_id = $request->promo_id;
        $coupon = $request->coupon;
        $promos = config('shop.promo_type');
        $promoOut = [
            'title' => null,
            'promo_id' => null,
            'coupon_id' => null,
            'score' => null,
            'percent' => null,
            'sum' => null,
            'products_id' => null,
            'qty' => null,
        ];

        // Сброс акций
        if ($promo === 'reset') {

            // Удаляем сессию promo
            session()->forget('promo');

            return redirect()->route('cart');
        }


        // Проверяем счёт
        elseif ($promo === 'score' && auth()->check()) {
            $promoOut['title'] = 'score';
            $promoOut['score'] = auth()->user()->score;
            $promoOut['sum'] = Promo::discountMax($promoOut['score']);

            // Запишем сессию акций
            session()->put('promo', $promoOut);

            return redirect()->route('cart');
        }


        // Проверяем купон
        elseif ($coupon) {
            $couponOb = Coupon::whereTitle($coupon)
                ->active()
                ->betweenTime();
            if ($couponOb) {
                $promoOut['coupon_id'] = $couponOb->id;
                $promoOut['title'] = 'coupon';

                // Если процент
                if ($couponOb->discount) {
                    $promoOut['sum'] = Promo::discountMax($couponOb->discount, true);

                    // Если сумма
                } else {
                    $promoOut['sum'] = Promo::discountMax($couponOb->price);
                }

                // Запишем сессию акций
                session()->put('promo', $promoOut);

                return redirect()->route('cart');
            }
        }


        // Проверяем акцию
        elseif (in_array($promo, $promos)) {

            // Получаем первую акцию по типу по сортировке
            $promoOb = Promo::active()
                ->betweenTime()
                ->orderBy('sort')
                ->whereType($promo)
                ->first();

            if ($promoOb) {

                // Расчёт для акции
                $method = 'calc' . Str::studly($promoOb->type);
                if (method_exists('\App\Models\Promo', $method)) {
                    $promoOut['title'] = $promo;
                    $promoOut['promo_id'] = $promoOb->id;
                    $promoTotal = Promo::$method($promoOb);
                    $promoOut['products_id'] = $promoTotal['products_id'] ?? null;
                    $promoOut['qty'] = $promoTotal['qty'] ?? null;
                    $promoOut['sum'] = Promo::discountMax($promoTotal['sum'] ?? null);

                    // Запишем сессию акций
                    session()->put('promo', $promoOut);

                    return redirect()->route('cart');
                }
            }
        }

        Main::getError('Request', __METHOD__);
    }


    /*

     Доставка
     'delivery' => [
            'name' => 'delivery_name',
            'sum' => 300.0,
        ]

     */
    public function delivery(Request $request)
    {
        $current = $request->delivery;
        $deliveryAll = config('shop.delivery');
        $delivery = null;

        // Найдём данные по доставке
        if ($current && $deliveryAll) {
            foreach ($deliveryAll as $value) {
                if (!empty($value['title'])&& $value['title'] === $current) {
                    $delivery = $value;
                    break;
                }
            }
            if ($delivery) {

                // Если сумма бесплатного лимита доставки больше суммы в корзине, то доставка бесплатная
                if (isset($delivery['sum'])) {
                    $deliverySum = isset($delivery['free_after']) && session()->has('cart.sum') && session('cart.sum') < $delivery['free_after'] ? $delivery['sum'] : 0;
                } else {
                    $deliverySum = 0;
                }

                // Запишем в сессию
                session()->put('delivery.title', $current);
                session()->put('delivery.sum', $deliverySum);
                session()->put('delivery.delivery', !empty($delivery['delivery']));

                return redirect()->route('cart');
            }
        }
        Main::getError('Request', __METHOD__);
    }


    /*public function getPaymentSberbank(Request $request)
    {

        //https://snipp.ru/php/sberbank-pay

        // Передать эти данные
        $orderId = 127;
        $sum = 1;

        $sum = $sum * 100; // Умножаем на 100, чтобы было в копейках
        $url = config('add.url');
        $domain = config('add.domain');
        $sbUrl = config('add.sberbank_url');

        $values['userName'] = config('add.sberbank_login');
        $values['password'] = config('add.sberbank_password');

        // Id заказа у нас
        $values['orderNumber'] = $orderId;

        // Корзина для чека (необязательно)
        $cart = [
            [
                'positionId' => 1,
                'name' => 'Название товара',
                'quantity' => [
                    'value' => 1,
                    'measure' => 'шт',
                ],
                'itemAmount' => 1000 * 100,
                'itemCode' => '123456',
                'tax' => [
                    'taxType' => 0,
                    'taxSum' => 0,
                ],
                'itemPrice' => 1000 * 100,
            ]
        ];

        // Если передаём корзину
        if (!empty($cart)) {
            $values['orderBundle'] = json_encode(['cartItems' => ['items' => $cart]], JSON_UNESCAPED_UNICODE);
        }

        // Сумма заказа в копейках
        $values['amount'] = $sum;

        // URL куда клиент вернется в случае успешной оплаты
        $values['returnUrl'] = "{$url}/success-payment/";

        // URL куда клиент вернется в случае ошибки
        $values['failUrl'] = "{$url}/error-payment/";

        // Описание заказа, не более 24 символов, запрещены % + \r \n
        $values['description'] = "Test Заказ №{$orderId} на {$domain}";

        $ch = curl_init("{$sbUrl}?" . http_build_query($values));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        // Получим объект заказа, если есть номер
        $order = Order::find($orderId);

        // Ответ от Сбербанка
        if (!empty($res)) {
            $res = json_decode($res, JSON_OBJECT_AS_ARRAY);

            // Проверим orderId и url
            if (!empty($res['orderId']) && !empty($res['formUrl'])) {


                // УСПЕШНЫЙ ОТВЕТ ОТ СБЕРБАНКА

                // Сохнаним id платежа от сбербанка
                $order->sberbank_id = $res['orderId'];
                $order->save();

                // Редирект на страницу банка для оплаты
                return redirect($res['formUrl']);
            }
        }

        // Возникла ошибка на стороне банка, то покажем её
        if (!empty($res['errorMessage'])) {
            session()->put('error', $res['errorMessage']);
        }

        return redirect()->route('error_payment');
    }*/
}
