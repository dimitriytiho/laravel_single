<?php

namespace App\Models;

use App\Traits\TModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use HasFactory, SoftDeletes, TModelScopes;


    protected $guarded = ['id', 'created_at', 'updated_at'];



    // Обратная связь один ко многим
    public function user() {
        return $this->belongsTo(User::class);
    }
}
