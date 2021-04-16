<?php

namespace App\Models;

use App\Traits\TModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes, TModelScopes;


    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать



    // Связь один ко многим внутри модели
    public function pages()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
