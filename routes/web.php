<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Если выключен веб-сайт, то редирект на страницу /error.php
if (env('OFF_WEBSITE')) {
    Route::domain(config('add.url'))->group(function () {
        header('Location: ' . env('APP_URL') . '/error.php');
        die;
    });
}


// Если в запросе /public, то сделается редирект на без /public
$url = request()->url();
$public = '/public';
if (stripos($url, $public) !== false) {
    $url = str_replace($public, '', $url);
    header("Location: {$url}");
    die;
}

$namespaceControllers = config('add.namespace_controllers');


// Admin
if (is_file($fileAdmin = __DIR__ . '/admin.php')) {
    require_once $fileAdmin;
}


// Shop
if (config('add.shop') && is_file($fileShop = __DIR__ . '/shop.php')) {
    require_once $fileShop;
}


// Auth
if (config('add.auth')) {

    Route::namespace("{$namespaceControllers}\\Auth")->group(function () {
        Route::get('auth/login', 'LoginController@showLoginForm')->name('login');
        Route::post('auth/login', 'LoginController@login')->name('login_post');
        Route::get('auth/logout', 'LoginController@logout')->name('logout');
        Route::post('auth/password/confirm', 'ConfirmPasswordController@confirm')->name('password.confirm_post');
        Route::get('auth/password/confirm', 'ConfirmPasswordController@confirm')->name('password.confirm');
        Route::post('auth/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('auth/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('auth/password/reset', 'ResetPasswordController@reset')->name('password.update');
        Route::get('auth/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
        Route::get('auth/register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('auth/register', 'RegisterController@register')->name('register_post');
    });


    // Личный кабинет
    Route::namespace("{$namespaceControllers}\\Home")
        ->prefix('home')
        ->name('home.')
        ->middleware('auth')
        ->group(function () {

        Route::get('/', 'HomeController@index')->name('index');
            Route::get('user', 'UserController@index')->name('user_index');
            Route::post('user/edit', 'UserController@edit')->name('user_edit');
            Route::get('order', 'OrderController@index')->name('order_index');
            Route::get('{id}/order', 'OrderController@show')->name('order_show');


        });
    //Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //Auth::routes();
}

Route::namespace($namespaceControllers)->group(function () {


    // Ex
    Route::prefix('ex')->name('ex.')->group(function () {

        Route::get('json', 'ExController@json')->name('json');
        Route::get('get', 'ExController@get')->name('get');
    });


    // Form
    Route::post('/contact-us', 'FormController@contactUs')->name('post_contact_us');


    // Page
    Route::get('/', 'PageController@index')->name('index');
    //Route::post('/set-cookie', 'PostController@setCookie')->name('set_cookie');
    if (config('add.search')) {
        Route::get('/search', 'SearchController@index')->name('search');
        Route::post('/search-js', 'SearchController@js')->name('search_js');
    }
    Route::get('/contact-us', 'PageController@contactUs')->name('contact_us');
    //Route::match(['get','post'], '/contact-us', 'PageController@contactUs')->name('contact_us');
    Route::get('/{slug}', 'PageController@show')->name('page');

});

/*Route::get('/', function () {
    return view('welcome');
});*/
