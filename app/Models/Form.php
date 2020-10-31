<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    use SoftDeletes;


    // Обратная связь один ко многим
    public function user() {
        return $this->belongsTo(User::class);
    }
}
