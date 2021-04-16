<?php

namespace App\Models;

use App\Traits\TModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuGroup extends Model
{
    use HasFactory, SoftDeletes, TModelScopes;


    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать



    // Связь один к многим
    public function menus()
    {
        return $this->hasMany(Menu::class, 'belong_id', 'id');
    }
}
