<?php

namespace App\Models;


class ProductGallery extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    // Обратная связь один к одному (один id и один товар)
    public function product()
    {
        return $this->belongsToMany(Product::class);
    }
}
