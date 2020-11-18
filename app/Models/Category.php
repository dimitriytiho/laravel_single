<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;



    // Связь один ко многим внутри модели
    public function categories()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
}
