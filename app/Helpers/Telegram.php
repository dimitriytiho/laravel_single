<?php


namespace App\Helpers;

use illuminate\Support\Facades\File;

class Telegram
{
    /*
     * Найти в поиске Telegram @BotFather и написать боту @BotFather
     * \newbot
     * Name - название
     * Name_bot - название бота
     * Узнать id написать боту @username, в ответе будет id
     *
     * Можно добавить бота в группу и после открыть https://api.telegram.org/bot<YourBOTToken>/getUpdates
     * В ответе найти новое id для бота, сохранить его в файл .env
     *
     * Открыть чат @Name или можно добавить в группу
     */

    private $url = 'https://api.telegram.org';
    private $token;
    private $chatId;


    private function __construct()
    {
        $this->token = env('TELEGRAM_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
    }


    /**
     *
     * @return string
     *
     * Отправить сообщение в чат Телеграм, возвращает в ответе json.
     * $message - сообщение.
     */
    public static function sendMessage($message) {
        if ($message) {

            $self = new self();
            $url = "{$self->url}/bot{$self->token}/sendMessage?chat_id={$self->chatId}&text=" . urlencode($message);

            $ch = curl_init();
            $option = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
            ];

            curl_setopt_array($ch, $option);
            $res = curl_exec($ch);
            curl_close($ch);

            return $res;
        }
        return null;
    }


    /**
     *
     * @return string
     *
     * Отправить файл в чат Телеграм, возвращает в ответе json.
     * $filePath - путь с названием файла.
     */
    public static function sendFile($filePath) {
        if (File::isFile($filePath)) {

            $self = new self();
            $url = "{$self->url}/bot{$self->token}/sendDocument";
            $file = new \CURLFile(realpath($filePath));

            $ch = curl_init();
            $option = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: multipart/form-data',
                ],
                CURLOPT_POSTFIELDS => ['chat_id' => $self->chatId, 'document' => $file],
            ];

            curl_setopt_array($ch, $option);
            $res = curl_exec($ch);
            curl_close($ch);

            return $res;
        }
        return null;
    }
}
