<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь один ко многим
    public function products() {
        return $this->hasMany(Product::class);
    }
}
