<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modifier extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь один к многим обратная
    public function modifier_groups()
    {
        return $this->belongsTo(ModifierGroup::class, 'parent_id', 'id');
    }
}
