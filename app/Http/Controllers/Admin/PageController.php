<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\TAdminBaseController;

class PageController extends AppController
{
    // Связанные таблицы для удаления
    protected $relatedDelete = [

        // Страницы
        'pages',
    ];


    // Массив гет ключей для поиска
    protected $queryArr = [
        'title',
        'slug',
        'status',
        'sort',
        'parent_id',
        'id',
    ];


    // Передать поля для вывода в index виде
    protected $thead = [
        'title' => 'l',
        'slug' => null,
        'status' => 'l',
        'sort' => null,
        'parent_id' => null,
        'id' => null,
    ];


    // Правила валидации для метода Store
    protected $validateStore = [
        'title' => 'required|string|max:250',
        'slug' => 'required|string|unique:$this->table|max:250',
    ];

    // Правила валидации для метода Update
    protected $validateUpdate = [
        'parent_id' => 'required|integer|min:0',
        'title' => 'required|string|max:250',
        'slug' => 'required|string|unique:$this->table,slug,$id|max:250',
    ];



    use TAdminBaseController;
}
