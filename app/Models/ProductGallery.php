<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGallery extends App
{
    // Обратная связь один к одному (один id и один товар)
    public function product()
    {
        return $this->belongsToMany(Product::class);
    }
}
