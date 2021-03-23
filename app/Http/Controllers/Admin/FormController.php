<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\TAdminBaseController;

class FormController extends AppController
{
    // Массив гет ключей для поиска
    protected $queryArr = [
        'ip',
        'user_id',
        'id',
    ];


    // Передать поля для вывода в index виде
    protected $thead = [
        'user_id' => null, // Вместо user_id покажем данные пользователя
        'id' => null,
    ];



    use TAdminBaseController;
}
