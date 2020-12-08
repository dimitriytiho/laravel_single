<?php

use Illuminate\Support\Str;


return [

    /*
    |--------------------------------------------------------------------------
    | Add custom settings
    |--------------------------------------------------------------------------
    */

    'search' => true, // Поиск по сайту
    'auth' => true, // Включить авторизацию на сайте
    'shop' => false, // Включить интернет-магазин


    // Список используемых локалей
    'locales' => [
        'ru',
        //'en',
    ],

    // Title для гланой страницы
    'title_main' => 'home',

    // Кол-во элементов на странице для пагинации
    'pagination' => 24,


    // Настройки для SCSS, при изменении настроек необходимо запустить метод \App\Helpers\Upload::resourceInit(); и перекомпилировать стили
    'scss' => [
        'primary' => '#ff5e5e',
        'dark' => '#000', // #292b37
        'secondary' => '#6c757d',
        'light' => '#eceff1',
        'light-light' => '#fafafa',
        'transition' => '.5',
    ],

    'height' => 600,

    // Указать IP, для которых запрещён доступ к сайту после этого нужно запустить команду \App\Helpers\Upload::htaccess();
    'banned_ip' => [
        '',
    ],

    'development' => [
        'Developer' => 'Dmitriy Konovalov',
        'Email' => 'dimitriyyuliya@gmail.com',
        'Facebook' => 'https://www.facebook.com/dimitriyyuliya',
        'From' => 'Moscow, Russia',
        'Language' => 'Russian',
        'Doctype' => 'HTML5',
        'Framework' => 'Laravel',
        'IDE' => 'PHPStorm, Visual Studio, Sublime Text, Photoshop, Illustrator',
        'Brand' => 'OmegaKontur',
    ],

    // Папка для картинок
    'img' => '/' . env('APP_IMG', 'img'),
    'imgPath' => public_path() . '/' . env('APP_IMG', 'img'),

    // Протокол и домен
    'protocol' => Str::before(env('APP_URL'), '://'),
    'domain' => Str::after(env('APP_URL'), '://'),

    // Статусы страниц
    'page_statuses' => [
        'inactive', // Неактивная должна стоять первой
        'active', // Активная должна стоять второй
    ],


    // SEO Настройки
    // Перечислить те страницы, которые не нужно индексировать
    'disallow' => [
        'search',
        'search/*',
        //'success-page',
    ],

    // Список таблиц информационных блоков (для обновления веб-сайта и пр.), у таблиц в структуре БД должны быть статусы как в массиве page_statuses.
    'list_of_information_block' => [

        // Имена таблиц в БД
        'tables' => [
            'pages',
        ],

        // Имена маршрутов из /routes/web.php, маршруты должны быть именованные
        'routes' => [ // Очерёдность должна быть как в массиве tables
            'page',
        ],
    ],

    // Список страниц, которые нужно добавить в sitemap, которых нет в БД
    'list_pages_for_sitemap_no_db' => [
        'items' => [
            //'contact-us',
            //'order',
        ],

        // Имена маршрутов из /routes/web.php, маршруты должны быть именованные
        'routes' => [ // Очерёдность должна быть как в массиве tables
            //'contact_us',
            //'order',
        ],
    ],


    'namespace_models' => 'App\\Models',
    'namespace_controllers' => 'App\\Http\\Controllers',
    'namespace_helpers' => 'App\\Helpers',


    // Настройки из файла /.env, т.к. после кэширования они будут возращать null
    'name' => env('APP_NAME', 'OmegaKontur'),
    'url' => env('APP_URL', '/'),
    'env' => env('APP_ENV', 'local'),
    'not_index_website' => env('NOT_INDEX_WEBSITE'), // Если не нужно индексировать сайт, то true, если нужно, то false

    'dev' => env('APP_DEV', 'OmegaKontur'),
    'enter' => env('APP_ENTER', 'login'),
    'admin' => env('APP_ADMIN', 'dashboard'),
    'app_email' => env('APP_EMAIL'),

    'youtube_api_key' => env('YOUTUBE_API_KEY'),
    'youtube_channel_id' => env('YOUTUBE_CHANNEL_ID'),

    'recaptcha_public_key' => env('RECAPTCHA_PUBLIC_KEY'),
    'recaptcha_secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'smsru' => env('SMSRU'),

    'sberbank_url' => env('SBERBANK_URL'),
    'sberbank_url_check' => env('SBERBANK_URL_CHECK'),
    'sberbank_login' => env('SBERBANK_LOGIN'),
    'sberbank_password' => env('SBERBANK_PASSWORD'),

];
