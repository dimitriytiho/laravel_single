<?php

use Illuminate\Support\Str;


// Для добавления загрузчика картинок в этот массив допишите название контроллера, например ProductGallery, далее в public/img создайте папку, например product-gallery
$controllers = [
    'User', // Пользователи
    'Portfolio', // Портфолио
    'PortfolioGallery', // Галерея портфолио
];
$arr = [];
$img = env('APP_IMG', 'img');
$path = public_path();

foreach ($controllers as $name) {
    $folder = Str::kebab($name);

    switch ($name) {
        case 'User':
            $arr += ["img{$name}" => "/{$img}/{$folder}"];
            $arr += ["imgPath{$name}" => "{$path}/{$img}/{$folder}"];
            $arr += ["img{$name}Default" => "/{$img}/default/no_user.png"];
            break;
        default:
            $arr += ["img{$name}" => "/{$img}/{$folder}"];
            $arr += ["imgPath{$name}" => "{$path}/{$img}/{$folder}"];
            $arr += ["img{$name}Default" => "/{$img}/default/no_image.jpg"];
            break;
    }
}

return $arr += [

    /*
    |--------------------------------------------------------------------------
    | Admin settings
    |--------------------------------------------------------------------------
    */

    'pagination' => [10, 25, 50, 100],
    'pagination_default' => 25, // По-умолчанию кол-во пагинации

    'locales' => [
        'ru',
        'en',
    ],

    'date_format' => 'dd.MM.y HH:mm', // d.m.Y H:i


    // Зоны доступа для пользователей
    'user_areas' => [
        1 => 'public', // Должна быть 1-я область для всего сайта
        2 => 'admin', // Должна быть 2-я область для админки сайта
    ],


    // Добавим контроллеры, которые нет в основной папке, для вывода в ролях
    'permission_add_controllers' => [
        'Admin\Log',
    ],
    // Контроллеры, которые пропустить
    'permission_skip_controllers' => [
        'App',
        'Admin\App',
        'Admin\Dummy',
        'Admin\Enter',
        'Admin\ImgUpload',
        'Admin\Main',
        'Ex',
        'Home',
        'Page',
        'Search',
    ],

    // Статусы пользователей
    'user_statuses' => [
        'secondary', // По-умолчанию, должен быть первый
        'info', // Повторно, должен быть втрой
        'success',
        'warning',
        'danger',
    ],


    // Выбор редактора для контента
    'editor' => 'codemirror', // Есть варианты: codemirror, ckeditor,


    // Статусы заказов
    'order_statuses' => [
        'new', // Должен быть первый new статус, т.е. новый
        'in_process',
        'completed',
        'canceled',
    ],


    // Типы настроек
    'setting_type' => [
        'string', // Первое значение по-умолчанию
        'checkbox', // Второй должен быть checkbox
    ],


    // Разрешённые для загрузки картинки
    'acceptedImagesExt' => [
        'jpg',
        'jpeg',
        'png',
        'gif',
    ],


    // Размер обычной картинки
    'imgWidth' => 800,
    'imgHeight' => 600,

    // Максимальные разрешения для картинок
    'imgMaxSizeHD' => 1920,
    'imgMaxSize' => 1280,
    'imgMaxSizeSM' => 500,

    // Максимальное кол-во картинок для Dropzone
    'maxFilesOne' => 1, // Для одиночной загрузки
    'maxFilesMany' => 30, // Для множественной загрузки

    // Картинки Webp
    'webp' => true,
    'webpQuality' => 80, // Качество до 100

];
