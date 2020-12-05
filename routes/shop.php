<?php

Route::namespace(config('add.namespace_controllers') . '\\Shop')->group(function () {

    // Cart
    Route::get('cart/{cart_key}/remove', 'CartController@remove')->name('cart_remove');
    Route::get('cart/{cart_key}/minus', 'CartController@minus')->name('cart_minus');
    Route::get('cart/{cart_key}/plus', 'CartController@plus')->name('cart_plus');
    Route::get('cart/{product_id}/add', 'CartController@add')->name('cart_add');
    Route::get('cart/show', 'CartController@show')->name('cart_show');
    Route::get('cart', 'CartController@index')->name('cart');

    Route::post('make-order', 'OrderController@makeOrder')->name('make_order');
    Route::get('catalog', 'CategoryController@index')->name('catalog');
    Route::get('category/{slug}', 'CategoryController@show')->name('category');
    Route::get('product/{slug}', 'ProductController@show')->name('product');

});
