<?php

namespace App\Models;

class ColorProduct extends App
{
    protected $table = 'color_product';
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать
}
