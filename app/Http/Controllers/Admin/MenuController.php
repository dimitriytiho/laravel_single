<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\TAdminBaseController;

class MenuController extends AppController
{
    // Является наследником для связанного элемента
    protected $belongChildren = true;

    // Условие выборки, название колонки
    protected $belongColumn = 'belong_id';

    // Связанная таблица, должен быть метод в моделе с названием таблицы
    protected $belongTable = 'menu_groups';

    // Связанный маршрут
    protected $belongRoute = 'menu-group';

    // Связанный элемент, возможность удалить
    protected $belongDelete = false;


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
        'belong_id' => 'required|integer',
        'title' => 'required|string|max:250',
    ];

    // Правила валидации для метода Update
    protected $validateUpdate = [
        'belong_id' => 'required|integer',
        'parent_id' => 'required|integer|min:0',
        'title' => 'required|string|max:250',
    ];



    use TAdminBaseController;
}
