<?php

namespace App\Models;


class PortfolioGallery extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    // Обратная связь один к одному (один id и один товар)
    public function portfolio()
    {
        return $this->belongsToMany(Portfolio::class);
    }
}
