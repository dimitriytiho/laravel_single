<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends App
{
    protected $table = 'menu';
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь многие к одному
    public function menuName()
    {
        return $this->belongsTo(MenuName::class, 'belong_id', 'id');
    }

    // Связь один ко многим внутри модели
    public function parents()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
