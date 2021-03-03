<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Promo extends App
{
    private $now;
    private $cart;
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }



    public function __construct()
    {
        parent::__construct();

        $this->now = date('Y-m-d h:i:s');
        $this->productsIdInCart = Cart::productsId();
        $this->cart = session()->has('cart') ? session('cart') : [];
        $this->model = __CLASS__;
    }



    /********************** Методы для опредения есть ли акции для корзины **********************/

    /**
     *
     * @return array
     *
     * Возвращает подходящии акции в массиве по сортировке.
     */
    public static function matches()
    {
        $matches = [];
        $self = new self();

        // Купон сохраннённый в сессии
        $coupon = session()->has('promo.title') && session('promo.title') === 'coupon';
        if ($coupon) {
            $matches['coupon'] = session('promo.sum');
        }

        // Балы на счёту у пользователя
        $score = self::score();
        if ($score) {
            $matches['score'] = $score;
        }

        // Другии акции
        $promos = config('shop.promo_type');
        if ($promos) {

            foreach ($promos as $key => $promo) {
                $item = Str::camel($promo);

                // Получает из БД активные, попадающии в промежуток времени, сортируем по сортировке и подходящии по типу акции. Кэшируем запросы.
                if (cache()->has("promo_matches_{$promo}")) {
                    $$promo = cache()->get("promo_matches_{$promo}");
                } else {
                    $$promo = $self::with('products')
                        ->active()
                        ->betweenTime()
                        ->orderBy('sort')
                        ->whereType($promo)
                        ->get();
                    cache()->forever("promo_matches_{$promo}", $$promo);
                }

                // Запускаем соответствующии методы
                $item = self::{$item}($$promo);
                if ($item) {
                    $matches[$promo] = $item;
                }
            }
        }

        return $matches;
    }

    /**
     *
     * @return integer
     *
     * Проверяет подходит ли акция: Балы на счёту у пользователя.
     * Возвращает кол-во балов на счету.
     */
    public static function score()
    {
        return auth()->check() ? auth()->user()->score : null;
    }

    /**
     *
     * @return integer
     *
     * Проверяет подходит ли акция: В корзине все товары из акции.
     * Возвращает id акции, первого совпадения.
     * Принимимает в объекте акции из БД, подходящии по типу.
     */
    public static function allProducts($promos)
    {
        $self = new self();
        if ($self->productsIdInCart && $promos->isNotEmpty()) {

            // Цикл по акциям
            foreach ($promos as $promo) {
                if ($promo->products->isNotEmpty()) {

                    // Цикл по товарам в акции
                    foreach ($promo->products as $product) {

                        // Если товара из акции нет в корзине, то к следующей акции
                        if (!$self->productsIdInCart->contains($product->id)) {
                            break 2; // Выходим из конструкции цикла по акциям
                        }
                    }
                }
                return $promo->id;
            }
        }
        return null;
    }

    /**
     *
     * @return integer
     *
     * Проверяет подходит ли акция: Один товар из акции.
     * Возвращает id акции, первого совпадения.
     * Принимимает в объекте акции из БД, подходящии по типу.
     */
    public static function oneProduct($promos)
    {
        $self = new self();
        if ($self->productsIdInCart && $promos->isNotEmpty()) {

            // Цикл по акциям
            foreach ($promos as $promo) {
                if ($promo->products->isNotEmpty()) {

                    // Цикл по товарам в акции
                    foreach ($promo->products as $product) {

                        // Если товар из акции есть в корзине, то возвращаем id акции
                        if ($self->productsIdInCart->contains($product->id)) {
                            return $promo->id;
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     *
     * @return integer
     *
     * Проверяет подходит ли акция: Товар в подарок.
     * Возвращает id акции, первого совпадения.
     * Принимимает в объекте акции из БД, подходящии по типу.
     */
    public static function productAsGift($promos)
    {
        if ($promos->isNotEmpty()) {
            $self = new self();
            $sum = $self->cart['sum'] ?? 0;

            // Цикл по акциям
            foreach ($promos as $promo) {

                // Если сумма в корзине больше цены акции, то возвращаем id акции
                if ($sum > $promo->price) {
                    return $promo->id;
                }
            }
        }
        return null;
    }

    /**
     *
     * @return integer
     *
     * Проверяет подходит ли акция: 2 одних и тех же товара 3-ий в подарок.
     * Возвращает id акции, первого совпадения.
     * Принимимает в объекте акции из БД, подходящии по типу.
     */
    public static function twoPlusOne($promos)
    {
        $self = new self();
        if ($self->productsIdInCart && $promos->isNotEmpty()) {

            // Если несколько товаров с одним id, посчитаем id в корзине
            $productsCount = $self->productsIdInCart->countBy();

            // Цикл по акциям
            foreach ($promos as $promo) {
                if ($promo->products->isNotEmpty()) {

                    // Цикл по товарам в акции
                    foreach ($promo->products as $product) {

                        // Получаем id товара в корзине
                        $productInCart = Cart::productInCart($product);

                        // Получаем кол-во товара в козине
                        $productInCart = $self->cart['products'][$productInCart]->qty ?? 0;


                        // Если товар из акции есть в корзине и его больше 1, то возвращаем id акции
                        if ($productInCart > 1 || isset($productsCount[$product->id]) && $productsCount[$product->id] > 1) {
                            return $promo->id;
                        }
                    }
                }
            }
        }
        return null;
    }


    /********************** Методы расчёта для перерасчёта корзины при выборе акций **********************/

    /**
     *
     * @return array
     *
     [
        'sum' => 200.0,
        'products_id' => [1, 2],
        'qty' => 1,
     ]
     *
     * Возвращает массив, в котором sum = 200.0 - сумма скидки, products_id = [1, 2] - в массиве id товаров подарков, qty = 1, сколько товаров в подарок.
     * $promo - принимает объект акции.
     */
    public static function calcAllProducts(Promo $promo)
    {
        $self = new self();
        $cartSum = $self->cart['sum'] ?? 0;

        if ($cartSum) {
            $sum = $promo->percent ? $cartSum / 100 * $promo->percent : $promo->price;
        }

        $ob = [
            'sum' => $sum ?? null, // Записываем цену акции
            'products_id' => null,
            'qty' => null,
        ];

        if ($self->productsIdInCart && $promo->products->isNotEmpty()) {

            // Цикл по товарам в акции
            foreach ($promo->products as $product) {

                // Если товара из акции нет в корзине
                if (!$self->productsIdInCart->contains($product->id)) {
                    return null;
                }
            }
            return $ob;
        }
        return null;
    }


    /**
     *
     * @return array
     *
    [
    'sum' => 200.0,
    'products_id' => [1, 2],
    'qty' => 1,
    ]
     *
     * Возвращает массив, в котором sum = 200.0 - сумма скидки, products_id = [1, 2] - в массиве id товаров подарков, qty = 1, сколько товаров в подарок.
     * $promo - принимает объект акции.
     */
    public static function calcOneProduct(Promo $promo)
    {
        $self = new self();
        $cartSum = $self->cart['sum'] ?? 0;

        if ($cartSum) {
            $sum = $promo->percent ? $cartSum / 100 * $promo->percent : $promo->price;
        }

        $ob = [
            'sum' => $sum ?? null, // Записываем цену акции
            'products_id' => null,
            'qty' => null,
        ];

        if ($self->productsIdInCart && $promo->products->isNotEmpty()) {

            // Цикл по товарам в акции
            foreach ($promo->products as $product) {

                // Если товар из акции есть в корзине
                if ($self->productsIdInCart->contains($product->id)) {
                    return $ob;
                }
            }
        }
        return null;
    }


    /**
     *
     * @return array
     *
    [
    'sum' => 200.0,
    'products_id' => [1, 2],
    'qty' => 1,
    ]
     *
     * Возвращает массив, в котором sum = 200.0 - сумма скидки, products_id = [1, 2] - в массиве id товаров подарков, qty = 1, сколько товаров в подарок.
     * $promo - принимает объект акции.
     */
    public static function calcProductAsGift(Promo $promo)
    {
        $self = new self();
        $ob = [
            'sum' => null,
            'products_id' => $promo->products->isNotEmpty() ? $promo->products->pluck('id') : null,
            'qty' => 1,
        ];
        $sum = $self->cart['sum'] ?? 0;

        // Если сумма в корзине больше цены акции
        if ($sum > $promo->price) {
            return $ob;
        }

        return null;
    }


    /**
     *
     * @return array
     *
    [
    'sum' => 200.0,
    'products_id' => [1, 2],
    'qty' => 1,
    ]
     *
     * Возвращает массив, в котором sum = 200.0 - сумма скидки, products_id = [1, 2] - в массиве id товаров подарков, qty = 1, сколько товаров в подарок.
     * $promo - принимает объект акции.
     */
    public static function calcTwoPlusOne(Promo $promo)
    {
        $self = new self();
        $ob = [
            'sum' => null,
            'products_id' => $promo->products->isNotEmpty() ? $promo->products->pluck('id') : null,
            'qty' => 1,
        ];

        if ($self->productsIdInCart && $promo->products->isNotEmpty()) {

            // Если несколько товаров с одним id, посчитаем id в корзине
            $productsCount = $self->productsIdInCart->countBy();

            // Цикл по товарам в акции
            foreach ($promo->products as $product) {

                // Получаем id товара в корзине
                $productInCart = Cart::productInCart($product);

                // Получаем кол-во товара в козине
                $productInCart = $self->cart['products'][$productInCart]->qty ?? 0;


                // Если товар из акции есть в корзине и его больше 1
                if ($productInCart > 1 || isset($productsCount[$product->id]) && $productsCount[$product->id] > 1) {
                    return $ob;
                }
            }
        }

        return null;
    }


    /**
     *
     * @return integer
     *
     * Возращает суммы скидки для корзины.
     * Если сумма превышает максимально установленный процет, то будет показана ошибка и запина сумма не больше максимальной.
     *
     * $sum - сумма скидки.
     * $percent - передать true, если в $sum передаётся процент скидки.
     */
    public static function discountMax($sum, $percent = null)
    {
        $self = new self();
        $cartSum = $self->cart['sum'] ?? 0;
        $discountMax = config('shop.discount_max') ?: 30;
        if ($cartSum && (float)$sum) {

            if ($percent) {
                $sum = $cartSum / 100 * $sum;
            }

            $sumMax = $cartSum / 100 * $discountMax;
            if ($sumMax < $sum) {
                $sum = $sumMax;

                // Запишем ошибку в сессию
                session()->flash('error', __('s.discount_not_exceed_percent_cart', ['percent' => $discountMax]));
            }
            return $sum;
        }
        return null;
    }
}
