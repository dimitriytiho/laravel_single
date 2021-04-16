<?php

namespace App\Models;

use App\Traits\TModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes, TModelScopes;


    protected $guarded = ['id', 'created_at', 'updated_at'];



    // Возвращает массив названий настроек, название которых нельзя изменить из панели управления
    public static function titleNoEditArr() {
        return [
            'name',
            'admin_email',
            'email',
            'tel',
            'date_format',
            'change_key',
        ];
    }
}
