<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AppController as App;

class AppController extends App
{
    protected $viewPath;


    public function __construct(Request $request)
    {
        parent::__construct();


        $viewPath = $this->viewPath = 'shop';

        View::share(compact('viewPath'));
    }
}
