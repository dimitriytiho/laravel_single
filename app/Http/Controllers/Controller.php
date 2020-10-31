<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Main;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected $class;
    protected $c;
    protected $model;
    protected $table;
    protected $route;
    protected $view;
    protected $namespaceModels = 'App\\Models';



    public function __construct()
    {
        $this->namespaceModels = config('add.namespace_models');
    }
}
