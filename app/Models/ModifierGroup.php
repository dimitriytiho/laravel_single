<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModifierGroup extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь один к многим обратная
    /*public function modifiers()
    {
        return $this->belongsTo(Modifier::class, 'parent_id');
    }*/

    // Связь один к многим
    public function modifiers()
    {
        return $this->hasMany(Modifier::class, 'parent_id');
    }

    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
