<?php

namespace App\Models;


use App\Helpers\Children;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;

class Main
{
    // Main - ВСПОМОГАТЕЛЬНЫЙ СТАТИЧНЫЙ КЛАСС

    /*
     * Пример использования паттерна Реестр (использовать в видах с \App\):
     * Main::$registry->set('test', 'testing'); - положить
     * dump(Main::$registry->get('test')); - достать
     * dump(Main::$registry->getAll); - достать всё
     */
    public static $registry;


    /*
     * Упрощение вызова паттерна Реестр (использовать в видах с \App\):
     * Main::set('test', 'testing'); - положить
     * dump(Main::get('test')); - достать
     */
    public static function set($name, $value)
    {
        if ($name) {
            self::$registry->set($name, $value);
        }
        return;
    }
    public static function get($value)
    {
        return self::$registry->get($value) ?? null;
    }


    /*
     * Возвращает настройку сайта.
     * Main::site('name') - достать настройку.
     * $settingName - название настройки.
     */
    public static function site($settingName)
    {
        return self::$registry->get('settings')[$settingName] ?? null;
    }


    /*
     * Подключает файл из /app/Modules/views/inc с название написаном в контенте ##!!!inc_name (название файла inc_name.blade.php).
     * $content - если передаётся контент, то в нём будет искаться ##!!!inc_name и заменяется на файл из папки inc.
     * $values - $values5 - Можно передать данные в подключаемый файл.
     */
    public static function inc(string $content = null, $values = null, $values2 = null, $values3 = null, $values4 = null, $values5 = null)
    {
        if ($content) {

            $search = '##!!!'; // \w+(?=##!!!) test##!!!    (?<=##!!!)\w+ ##!!!test
            $pattern = '/(?<=' . $search . ')\w+/';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            if ($matches) {

                foreach ($matches as $v) {
                    $view = "inc.inc_{$v[0]}";
                    $pattern_inner = '/' . $search . $v[0] . '/';

                    if (view()->exists($view)) {

                        $output = view($view, compact('values', 'values2', 'values3', 'values4', 'values5'))->render();
                        $content = preg_replace($pattern_inner, $output, $content, 1);
                    } else {
                        $content = preg_replace($pattern_inner, '', $content);
                    }
                }
            }
        }
        return $content;
    }


    /*
     * Использовать скрипты в контенте, они будут перенесены вниз страницы.
     * $content - контент, в котором удалиться скрипты и перенести их вниз страницы.
     * В шаблоне вида получить скрипты с помощью Main::get('scripts').
     */
    public static function getDownScript($content)
    {
        if ($content) {
            $scripts = [];
            $pattern = "^<script.*?>.*?</script>^si";
            preg_match_all($pattern, $content, $scripts);

            if (!empty($scripts[0])) {
                self::$registry->set('scripts', $scripts[0]);
                $content = preg_replace($pattern, '', $content);
            }
            return $content;
        }
        return false;
    }


    /*
     * Возвращает строку: URL, Email, IP пользователя.
     * $referer - передать true, если нужно вывести страницу, с которой перешёл пользователь, необязательный параметр.
     */
    public static function dataUser($referer = null)
    {
        $email = auth()->check() && isset(auth()->user()->email) ? '. Email: ' . auth()->user()->email . '.' : null;
        if ($referer) {
            $referer = !empty(request()->server('HTTP_REFERER')) ? '. Referer: ' . request()->server('HTTP_REFERER') . '. ' : null;
        }
        return "URL: " . request()->url() . "{$email} IP: " . request()->ip() . ". {$referer}";
    }


    /*
     * Если вида не существует, то записывает в логи ошибку и выбрасывает исключение.
     * $view - название вида (page.index).
     * $method - передать __METHOD__.
     */
    public static function viewExists(string $view, $method)
    {
        if (!view()->exists($view)) {
            $message = "View $view not found. " . self::dataUser() . "Error in {$method}";
            Log::critical($message);
            abort('404', $message);
        }
        return;
    }


    /*
     * Записывает в логи ошибку и выбрасывает исключение (если выбрано).
     * $message - текст сообщения.
     * $method - передать __METHOD__.
     * $abort - выбросывать исключение, по-умолчанию true, необязательный параметр.
     * $error - в каком виде записать ошибку, может быть: emergency, alert, critical, error, warning, notice, info, debug. По-умолчанию error, необязательный параметр.
     */
    public static function getError(string $message, $method, $abort = true, $error = 'warning')
    {
        $message = "{$message}. " . self::dataUser() . "In {$method}";
        Log::$error($message);
        if ($abort) {
            abort('404', $message);
        }
        return;
    }


    // Возвращает URL без префикса языка и без папки public.
    public static function notPublicInURL()
    {
        $url = request()->url();
        //$url = str_replace('/public', '', $url);
        return $url;
    }
}
