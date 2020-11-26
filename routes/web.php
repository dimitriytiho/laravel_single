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
        Route::get('login', 'LoginController@showLoginForm')->name('login');
        Route::post('login', 'LoginController@login')->name('login_post');
        Route::get('logout', 'LoginController@logout')->name('logout');
        Route::post('password/confirm', 'ConfirmPasswordController@confirm')->name('password.confirm_post');
        Route::get('password/confirm', 'ConfirmPasswordController@confirm')->name('password.confirm');
        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'RegisterController@register')->name('register_post');
    });

    //Auth::routes();
    //Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
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
