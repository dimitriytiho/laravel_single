<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
