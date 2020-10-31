<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Libs\Registry;
use App\Models\Main;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // ЗДЕСЬ ПИСАТЬ КОД, КОТОРЫЙ ЗАПУСКАЕТСЯ ПОСЛЕ ЗАГРУЗКИ ВСЕХ СЕРВИС-ПРОВАЙДЕРОВ


        // Определить мобильную версию
        $detect = new \Mobile_Detect();
        $isMobile = $detect->isMobile();
        //Main::set('isMobile', $isMobile);

        // Подключаем вспомогательные библиотеки из /app/Libs
        $lib = app_path('Libs');
        $functionFile = "{$lib}/function.php";
        $constructorFile = "{$lib}/construct.php";
        if (File::isFile($functionFile)) {
            require_once $functionFile;
        }
        if (File::isFile($constructorFile)) {
            require_once $constructorFile;
        }

        // Паттерн реестр
        Main::$registry = Registry::instance();


        // Добавляем Google ReCaptcha в валидатор
        /*Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            $recaptcha = new ReCaptcha(config('add.recaptcha_secret_key'));
            $resp = $recaptcha->verify($value, request()->ip());

            return $resp->isSuccess();
        });*/

        // Валидатор номера телефона (допускаются +()- и цифры)
        Validator::extend('tel', function($attribute, $value, $parameters) {
            return preg_match('#^[\+\(\)\- 0-9]+$#', $value) && strlen($value) > 10;
        });


        // Если индексирование сайта выключено
        if (config('add.not_index_website')) {
            header('X-Robots-Tag: noindex,nofollow'); // Заголовок запрещающий индексацию сайта
        }


        /*
         * Использовать: echo Main::site('name');
         *
         * Дополнительные варианты:
         * echo Main::get('settings')['name'];
         */
        if (cache()->has('settings_for_site')) {
            $settings = cache()->get('settings_for_site');

        } else {
            $settings = DB::table('settings')->get();

            // Кэшируется запрос
            cache()->forever('settings_for_site', $settings);
        }
        if (!empty($settings)) {
            $part = [];
            foreach ($settings as $v) {
                $part[$v->title] = $v->value;
            }
            Main::set('settings', $part);
        }


        // Если не вызван метод \App\Helpers\App\setMeta(), то по-умолчанию мета: title - название сайта, тег description - пустой
        $siteName = Main::site('name') ?: config('add.name');
        $getMeta = "<title>{$siteName}</title>\n\t<meta name='description' content=''>\n";


        // Кононический Url без Get параметров
        $cononical = Main::notPublicInURL();


        // Название папки для картинок в public
        $img = config('add.img', 'img');


        // Передаём в виды переменные
        view()->share(compact('isMobile', 'getMeta', 'cononical', 'img'));
    }
}
