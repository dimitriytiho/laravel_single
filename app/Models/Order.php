<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Str as HelpersStr;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class Order extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    use SoftDeletes;


    // Обратная связь один ко многим
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function users() {
        return $this->belongsTo(User::class);
    }


    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    // Связь одим ко многим
    public function order_product()
    {
        return $this->hasMany(OrderProduct::class);
    }



    // Возвращает класс html для статуса заказа, принимает статуса заказа.
    public static function orderStatusColorClass($status)
    {
        switch ($status) {
            case 'in_process':
                return 'warning';
            case 'completed':
                return 'info';
            case 'canceled':
                return 'secondary'; // text-through
            default:
                return 'success';
        }
    }


    /**
     *
     * @return array
     *
     * Оплата пластиковыми картами через Сбербанк.
     * Возвращает массив, в котором url при успехе или ошибка если есть.
     * $order - объект заказа.
     * https://snipp.ru/php/sberbank-pay
     *
     *
     * Тестовый Url регистрации заказа:
     * https://3dsec.sberbank.ru/payment/rest/register.do
     *
     * Тестовый Url проверки заказа:
     * https://3dsec.sberbank.ru/payment/rest/getOrderStatusExtended.do
     *
     *
     * Боевой Url регистрации заказа:
     * https://securepayments.sberbank.ru/payment/rest/register.do
     *
     * Боевой Url проверки заказа:
     * https://securepayments.sberbank.ru/payment/rest/getOrderStatusExtended.do
     *
     * Тестовая карта для проверки на тестовом Url
     * Номер: 4111 1111 1111 1111
     * Дата: 12/24
     * Проверочный код на обратной стороне:	123
     * Проверочный код 3-D Secure: 12345678
     */
    public static function getPaymentSberbank($order)
    {
        // Передать эти данные
        $orderId = $order->id ?? null;
        $sum = $order->sum ?? null;

        if ($orderId && $sum) {

            $sum = $sum * 100; // Умножаем на 100, чтобы было в копейках
            $url = config('add.url');
            $domain = config('add.domain');
            $sbUrl = config('add.sberbank_url');

            $values['userName'] = config('add.sberbank_login');
            $values['password'] = config('add.sberbank_password');

            // Id заказа у нас
            $values['orderNumber'] = $orderId;

            // Корзина для чека (необязательно)
            /*$cart = [
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
            ];*/

            // Если передаём корзину
            /*if (!empty($cart)) {
                $values['orderBundle'] = json_encode(['cartItems' => ['items' => $cart]], JSON_UNESCAPED_UNICODE);
            }*/

            // Сумма заказа в копейках
            $values['amount'] = $sum;

            // Url куда клиент вернется в случае успешной оплаты
            $values['returnUrl'] = "{$url}/success-payment";

            // Url куда клиент вернется в случае ошибки
            $values['failUrl'] = "{$url}/error-payment";

            // Описание заказа, не более 24 символов, запрещены % + \r \n
            $values['description'] = "Test Заказ №{$orderId} на {$domain}";

            // Делаем Curl запрос
            $ch = curl_init("{$sbUrl}?" . http_build_query($values)); // https://3dsec.sberbank.ru/payment/rest/register.do
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $res = curl_exec($ch);
            curl_close($ch);

            // Ответ от Сбербанка
            if (!empty($res)) {
                $res = json_decode($res, JSON_OBJECT_AS_ARRAY);

                // Проверим orderId и url
                if (!empty($res['orderId']) && !empty($res['formUrl'])) {


                    // УСПЕШНЫЙ ОТВЕТ ОТ СБЕРБАНКА

                    // Сохнаним id платежа от сбербанка
                    $order->sberbank_id = $res['orderId'];
                    $order->save();

                    // Полученный в ответе банка Url
                    $url = $res['formUrl'];
                }
            }

            // Если получена ошибка на стороне банка
            if (!empty($res['errorMessage'])) {
                $error = $res['errorMessage'];
            }
        }
        return [
            'url' => $url ?? null,
            'error' => $error ?? null,
        ];
    }


    /**
     *
     * @return bool
     *
     * Проверяет ответ от Сбербанка, возвращает true или false.
     * $orderId - номер заказа, который присвоил $orderId Сбербанк.
     */
    public static function checkPaymentSberbank($orderId)
    {
        /*

        Ответ от Сбербанка:
        Array
            (
                [errorCode] => 0
                [errorMessage] => Успешно
                [orderNumber] => 123
                [orderStatus] => 1
                [actionCode] => 0
                [actionCodeDescription] =>
                [amount] => 1000
                [currency] => 643
                [date] => 1540207733683
                [orderDescription] => Заказ №123 на example.com
                [ip] => 192.168.27.138
                [merchantOrderParams] => Array()
                [attributes] => Array(
                    [0] => Array(
                        [name] => mdOrder
                        [value] => 70906e55-7114-41d6-8332-4609dc6590f4
                    )
                )
                [cardAuthInfo] => Array(
                    [expiration] => 201912
                    [cardholderName] => CARDHOLDER NAME
                    [approvalCode] => 123456
                    [pan] => 411111XXXXXX1111
                )
                [authDateTime] => 1540207881419
                [terminalId] => 123456
                [authRefNum] => 111111111111
                [paymentAmountInfo] => Array(
                    [paymentState] => APPROVED
                    [approvedAmount] => 1000
                    [depositedAmount] => 0
                    [refundedAmount] => 0
                )
                [bankInfo] => Array(
                    [bankName] => TEST CARD
                    [bankCountryCode] => RU
                    [bankCountryName] => Россия
                )
            )

         */
        if ($orderId) {

            // Получаем заказ из БД
            $self = new self();
            $order = $self::where('sberbank_id', $orderId)->first();

            if ($order) {

                // Получаем данные от Сбербанка
                $sbUrl = config('add.sberbank_url_check');
                $values['userName'] = config('add.sberbank_login');
                $values['password'] = config('add.sberbank_password');
                $values['orderId'] = $orderId;

                $ch = curl_init("{$sbUrl}?" . http_build_query($values));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $res = curl_exec($ch);
                curl_close($ch);


                $res = json_decode($res, JSON_OBJECT_AS_ARRAY);

                // Успешные статусы оплаты 1 и 2
                $statusSuccess = $res['orderStatus'] ?? null;
                $statusSuccess = $statusSuccess == 1 || $statusSuccess == 2;

                // Проверим номер заказа из БД и полученный от Сбербанка
                if ($statusSuccess && !empty($res['orderNumber']) && $res['orderNumber'] == $order->id) {

                    // Запишем что заказ оплачен
                    $order->paid = '1';
                    $order->save();

                    // Получаем пользователя
                    $user = User::find($order->user_id);

                    if ($user) {

                        $userInfo = " Оплатил пользователь {$user->name} {$user->email} {$user->tel}.";

                        // Письмо пользователю
                        try {
                            $title = 'Успешная оплата на ' . config('add.domain');
                            $body = "Оплата заказа №{$order->id} пластиковой картой прошла успешно. Спасибо, что пользуйтесь нашими услугами.";

                            // Отправить письмо
                            Mail::to($user->email)
                                ->send(new SendMail($title, $body));

                        } catch (\Exception $e) {
                            Main::getError("Error sending email admin: $e", __METHOD__, false);
                        }
                    }


                    // Письма админам
                    $emailAdmin = HelpersStr::strToArr(Main::site('admin_email'));

                    if (!empty($emailAdmin[0])) {
                        try {
                            $title = 'Успешная оплата на ' . config('add.domain');
                            $body = "Оплата заказа №{$order->id} пластиковой картой прошла успешно."  . $userInfo ?? null;

                            // Отправить письмо
                            Mail::to($emailAdmin)
                                ->send(new SendMail($title, $body));

                        } catch (\Exception $e) {
                            Main::getError("Error sending email admin: $e", __METHOD__, false);
                        }
                    }


                    // ВСЁ ПРОШЛО УСПЕШНО
                    return true;



                    // Если получен ответ от Сбербанка, но данные не совпали или статус ошибки
                } else {

                    $orderStatus = config('shop.sberank_order_status');

                    // Покажем статус от Сбербанка
                    if (!empty($orderStatus[$res['orderStatus']])) {
                        session()->put('error', $orderStatus[$res['orderStatus']]);
                    }
                }
            }
        }
        return false;
    }
}
