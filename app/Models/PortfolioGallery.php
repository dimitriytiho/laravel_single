<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioGallery extends App
{
    // Обратная связь один к одному (один id и один товар)
    public function portfolio()
    {
        return $this->belongsToMany(Portfolio::class);
    }
}
