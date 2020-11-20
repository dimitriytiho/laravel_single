<?php

Route::namespace(config('add.namespace_controllers') . '\\Shop')->group(function () {

    // Получаем товары через Ajax
    Route::post('category/get-product', 'CategoryController@getProduct')->name('category_get_product');

    // Оплата
    //Route::get('payment', 'OrderController@getPaymentSberbank')->name('payment');

    Route::post('cart/product-in-cart-action', 'CartController@productInCartAction')->name('cart_product_in_cart_action');
    Route::post('cart/{product_id}/product-in-cart', 'CartController@productInCart')->name('cart_product_in_cart');

    Route::get('cart/{product_id}/destroy', 'CartController@destroy')->name('cart_destroy');
    Route::get('cart/{product_id}/minus', 'CartController@minus')->name('cart_minus');
    Route::get('cart/{product_id}/plus', 'CartController@plus')->name('cart_plus');
    Route::get('cart/show', 'CartController@show')->name('cart_show');
    Route::get('cart', 'CartController@index')->name('cart');

    Route::post('make-order', 'OrderController@makeOrder')->name('make_order');
    //Route::get('catalog', 'CategoryController@index')->name('catalog');
    Route::get('category/{slug}', 'CategoryController@show')->name('category');
    Route::get('product/{slug}', 'ProductController@show')->name('product');

});
