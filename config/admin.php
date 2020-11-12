<?php

return [

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
        'secondary', // По-умолчанию
        'info',
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
    ],


    // Разрешённые для загрузки картинки
    'acceptedImagesExt' => [
        'jpg',
        'jpeg',
        'png',
        'gif',
    ],


    // Картинки - для новых, используйте название как написаны, в конце подставляя свои, например imgBrand
    // Пользователи
    'imgUser' => '/' . env('APP_IMG', 'img') . '/users-photo',
    'imgPathUser' => public_path() . '/' . env('APP_IMG', 'img') . '/users-photo',
    'imgUserDefault' => '/' . env('APP_IMG', 'img') . '/default/no_user.png',

    // Категории
    'imgCategory' => '/' . env('APP_IMG', 'img') . '/category',
    'imgPathCategory' => public_path() . '/' . env('APP_IMG', 'img') . '/category',
    'imgCategoryDefault' => '/' . env('APP_IMG', 'img') . '/default/no_image.jpg',

    // Товары
    'imgProduct' => '/' . env('APP_IMG', 'img') . '/product',
    'imgPathProduct' => public_path() . '/' . env('APP_IMG', 'img') . '/product',
    'imgProductDefault' => '/' . env('APP_IMG', 'img') . '/default/no_image.jpg',

    // Галерея товаров
    'imgProductGallery' => '/' . env('APP_IMG', 'img') . '/product-gallery',
    'imgPathProductGallery' => public_path() . '/' . env('APP_IMG', 'img') . '/product-gallery',

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
