<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Code extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;
}
