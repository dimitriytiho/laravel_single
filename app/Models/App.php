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


    // Scope для активных элементов, использование ->active()
    public function scopeActive($query)
    {
        $statusActive = config('add.page_statuses')[1] ?: 'active';
        return $query->where('status', $statusActive);
    }
}
