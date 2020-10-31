<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    use SoftDeletes;


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
