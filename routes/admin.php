<?php

use App\Models\Upload;


$namespaceControllers = config('add.namespace_controllers');
$namespace = "{$namespaceControllers}\\Admin";
$admin = config('add.admin', 'dashboard');

//Route::namespace($namespace)->prefix($admin)->get('/', 'MainController@index')->name('admin.main')->middleware('admin');

// Страница входа в админку. Если включена авторизация, то админы авторизируется в публичной части сайта.
if (!config('add.auth')) {
    Route::namespace($namespace)->name(config('add.enter'))->group(function () {

        $key = Upload::getKeyAdmin();
        $keyRoute = "enter/{$key}";
        Route::post($keyRoute, 'EnterController@enterPost')->name('_post');
        Route::get($keyRoute, 'EnterController@index');

    });
}


// Роуты для админки
Route::namespace($namespace)
    ->prefix($admin)->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
    //Route::post('/menu/index', 'Admin\MenuController@index')->name('menu.index.post');

    // Routes import export
    Route::get('import-export', 'ImportExportController@view')->name('import_export');
    // Route export User
    Route::get('export-user', 'ImportExportController@exportUser')->name('export_user');

    // Если включен shop
    if (config('add.shop')) {

        // Product
        Route::get('export-product', 'ImportExportController@exportProduct')->name('export_product');
        Route::post('import-product', 'ImportExportController@importProduct')->name('import_product');
        // Category
        Route::get('export-category', 'ImportExportController@exportCategory')->name('export_category');
        Route::post('import-category', 'ImportExportController@importCategory')->name('import_category');


        // Shop controllers
        Route::post('product-add-category', 'CategoryProductController@productAdd')->name('product_add_category');
        Route::post('product-destroy-category', 'CategoryProductController@productDestroy')->name('product_destroy_category');

        Route::resource('order', 'OrderController')->only(['index', 'show', 'update', 'destroy']);
        Route::resource('category', 'CategoryController')->except(['show']);
        Route::resource('product', 'ProductController')->except(['show']);

        // Filters
        Route::resource('filter-group', 'FilterGroupController')->except(['show']);
        Route::resource('filter-value', 'FilterValueController')->except(['show']);
        Route::post('product-add-filter', 'FilterProductController@productAdd')->name('product_add_filter');
        Route::post('product-destroy-filter', 'FilterProductController@productDestroy')->name('product_destroy_filter');
    }


    // Website controllers resource
    Route::resource('form', FormController::class)->only(['index', 'show', 'destroy']);
    Route::resource('page', PageController::class)->except(['show']);
    Route::resource('user', UserController::class)->except(['show']);
    Route::resource('menu-name', MenuNameController::class)->except(['show']);
    Route::resource('menu', MenuController::class)->except(['show']);
    Route::resource('setting', SettingController::class)->except(['show']);
    Route::resource('translate', TranslateController::class)->except(['show']);
    //Route::resource('form', FormController::class);
    //Route::resource('form', 'FormController);


    // Website add controllers
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::match(['get','post'],'additionally', 'AdditionallyController@index')->name('additionally');
    Route::get('/additionally/files', 'AdditionallyController@files')->name('files');


    // Add routes get
    Route::get('sidebar-mini', 'MainController@sidebarMini')->name('sidebar_mini');
    Route::get('get-cookie', 'MainController@getCookie')->name('get_cookie');
    Route::get('pagination', 'MainController@pagination')->name('pagination');
    Route::get('locale/{locale}', 'MainController@locale')->name('locale');
    Route::get('/', 'MainController@index')->name('main');
    Route::get('logout', 'UserController@logout')->name('logout');

    // Add routes post
    Route::post('get-slug', 'MainController@getSlug')->name('get_slug');
    Route::post('img-remove', 'ImgUploadController@remove')->name('img_remove');
    Route::post('img-upload', 'ImgUploadController@upload')->name('img_upload');

    // Если не включена авторизация на сайте
    if (!config('add.auth')) {
        Route::post('to-change-key', 'MainController@toChangeKey')->name('key_to_enter');
    }
});
