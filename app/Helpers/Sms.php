<?php


namespace App\Helpers;

use App\Libs\SMSRU;

class Sms
{
    /*
     * Отправить одно СМС.
     * $phoneNumber - принимает телефонный номер.
     * $textMessage - текст сообщения, максимально 70 знаков.
     * $from - Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию (например OmegaKontur).
     */
    public static function sendOneSMS($phoneNumber, $textMessage, $from = '')
    {
        $tel = onlyPhoneNumber($phoneNumber);
        if ($tel) {
            $smsru = new SMSRU(config('add.smsru')); // Ваш уникальный программный ключ, который можно получить на главной странице

            $data = new \stdClass();
            $data->to = $tel;
            $data->text = $textMessage;
            $data->from = $from;
// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
// $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
// $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
            $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную

            return $sms;

            /*if ($sms->status == "OK") { // Запрос выполнен успешно
                echo "Сообщение отправлено успешно. ";
                echo "ID сообщения: $sms->sms_id. ";
                echo "Ваш новый баланс: $sms->balance";
            } else {
                echo "Сообщение не отправлено. ";
                echo "Код ошибки: $sms->status_code. ";
                echo "Текст ошибки: $sms->status_text.";
            }*/
        }
        return false;
    }
}
