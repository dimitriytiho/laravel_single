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
    public function users() {
        return $this->belongsTo(User::class);
    }


    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    // Связь одим ко многим
    public function order_product()
    {
        return $this->hasMany(OrderProduct::class);
    }



    // Возвращает класс html для статуса заказа, принимает статуса заказа.
    public static function orderStatusColorClass($status)
    {
        switch ($status) {
            case 'in_process':
                return 'warning';
            case 'completed':
                return 'info';
            case 'canceled':
                return 'secondary'; // text-through
            default:
                return 'success';
        }
    }
}
