<?php

use App\Models\Upload;


$namespaceControllers = config('add.namespace_controllers');
$namespace = "{$namespaceControllers}\\Admin";
$admin = config('add.admin', 'dashboard');

//Route::namespace($namespace)->prefix($admin)->get('/', 'MainController@index')->name('admin.main')->middleware('admin');

// Страница входа в админку. Если включена авторизация, то админы авторизируется в публичной части сайта.
if (!config('add.auth')) {

    Route::namespace($namespace)->name(env('APP_ENTER'))->group(function () {
        $key = Upload::getKeyAdmin();
        $keyRoute = env('APP_ENTER') . "/{$key}";

        Route::get($keyRoute, 'EnterController@index');
        Route::post($keyRoute, 'EnterController@login')->name('_post');

    });
}


// Роуты для админки
Route::namespace($namespace)
    ->prefix($admin)
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

    // Routes import export
    Route::get('import-export', 'ImportExportController@view')->name('import_export');
    // Route export User
    Route::get('export-user', 'ImportExportController@exportUser')->name('export_user');



    // Website controllers resource
    Route::resource('form', FormController::class)->only(['index', 'show', 'destroy']);
    Route::resource('page', PageController::class)->except(['show']);
    Route::resource('user', UserController::class)->except(['show']);
    Route::resource('role', RoleController::class)->except(['show']);
    Route::resource('menu-group', MenuGroupController::class)->except(['show']);
    Route::resource('menu', MenuController::class)->except(['show']);
    Route::resource('setting', SettingController::class)->except(['show']);
    Route::resource('file', FileController::class)->except(['show']);
    //Route::resource('translate', TranslateController::class)->except(['show']);


    // Website add controllers
    Route::get('log', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::match(['get','post'],'additionally', 'AdditionallyController@index')->name('additionally');
    Route::get('/additionally/files', 'AdditionallyController@files')->name('files');


    // Add routes get
    Route::get('delete-img', 'ImgUploadController@deleteImg')->name('delete_img');
    Route::get('sidebar-mini', 'MainController@sidebarMini')->name('sidebar_mini');
    Route::get('get-cookie', 'MainController@getCookie')->name('get_cookie');
    Route::get('pagination', 'MainController@pagination')->name('pagination');
    Route::get('locale/{locale}', 'MainController@locale')->name('locale');
    Route::get('/', 'MainController@index')->name('main');
    Route::get('logout', 'UserController@logout')->name('logout');

    // Add routes post
    //Route::post('new-order', 'MainController@newOrder')->name('new_order');
    Route::post('get-slug', 'MainController@getSlug')->name('get_slug');
    Route::post('img-remove', 'ImgUploadController@remove')->name('img_remove');
    Route::post('img-upload', 'ImgUploadController@upload')->name('img_upload');

    // Если не включена авторизация на сайте
    if (!config('add.auth')) {
        Route::post('to-change-key', 'MainController@toChangeKey')->name('key_to_enter');
    }
});
