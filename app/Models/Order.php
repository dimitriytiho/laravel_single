<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    use SoftDeletes;


    // Обратная связь один ко многим
    public function user() {
        return $this->belongsTo(User::class);
    }


    // Связь один ко многим (один заказ и много записей в order_product)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
