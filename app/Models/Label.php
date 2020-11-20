<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать
    //protected $fillable = ['title', 'description']; // Разрешается редактировать


    use SoftDeletes;


    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class, 'label_product');
    }
}
