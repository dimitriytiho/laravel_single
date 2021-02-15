<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filter extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь один к многим обратная
    public function filterGroup()
    {
        return $this->belongsTo(FilterGroup::class);
    }


    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
