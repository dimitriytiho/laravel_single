<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;


    // Расширяем модель
    protected $class;
    protected $model;
    protected $table;
    protected $view;


    public function __construct()
    {
        parent::__construct();
    }
}
