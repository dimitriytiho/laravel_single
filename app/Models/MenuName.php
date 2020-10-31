<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuName extends App
{
    protected $table = 'menu_names';
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь один к многим
    public function menu()
    {
        return $this->hasMany(Menu::class, 'belong_id', 'id');
    }
}
