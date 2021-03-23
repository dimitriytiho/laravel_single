<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\TAdminBaseController;

class MenuGroupController extends AppController
{
    // Связанные таблицы для удаления
    protected $relatedDelete = [

        'menus',
    ];


    // Связанная таблица, должен быть метод в моделе с названием таблицы
    protected $belongTable = 'menus';


    // Массив гет ключей для поиска
    protected $queryArr = [
        'title',
        'sort',
        'id',
    ];


    // Передать поля для вывода в index виде
    protected $thead = [
        'title' => 'l',
        'sort' => null,
        'id' => null,
    ];


    // Правила валидации для метода Store
    protected $validateStore = [
        'title' => 'required|string|unique:$this->table|max:250',
    ];

    // Правила валидации для метода Update
    protected $validateUpdate = [
        'title' => 'required|string|unique:$this->table,title,$id|max:250',
    ];



    use TAdminBaseController;
}
