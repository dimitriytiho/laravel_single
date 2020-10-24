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


// Если нужно выключить веб-сайт, то раскомментируйте
/*Route::domain(config('add.url'))->group(function () {
    // Редирект на страницу /public/error.php
    header('Location: ' . env('APP_URL') . '/error.php');
    die;
});*/


// Если в запросе /public, то сделается редирект на без /public
$url = request()->url();
$public = '/public';
if (stripos($url, $public) !== false) {
    $url = str_replace($public, '', $url);
    header("Location: {$url}");
    die;
}

$namespaceControllers = config('add.namespace_controllers');


// Auth
if (config('add.auth')) {
    Auth::routes();
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
}


// Page
Route::namespace($namespaceControllers)->group(function () {

    Route::get('/', 'PageController@index')->name('index');
    //Route::post('/set-cookie', 'PostController@setCookie')->name('set_cookie');
    if (config('add.search')) {
        Route::get('/search', 'SearchController@index')->name('search');
        Route::post('/search-js', 'SearchController@js')->name('search_js');
    }
    Route::match(['get','post'], '/contact-us', 'PageController@contactUs')->name('contact_us');
    Route::get('/{slug}', 'PageController@show')->name('page');
});

/*Route::get('/', function () {
    return view('welcome');
});*/
