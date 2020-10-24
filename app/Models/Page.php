<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Page extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at'];


    use HasFactory;


    public function __construct()
    {
        parent::__construct();

        $this->class = class_basename(__CLASS__);
        $this->model = "\\App\\Models\\{$this->class}";
        $this->table = with($this)->getTable();
        $this->view = Str::snake($this->class);
    }
}
