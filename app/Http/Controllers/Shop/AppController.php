<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\AppController as App;

class AppController extends App
{
    public function __construct(Request $request)
    {
        parent::__construct();

        $viewPath = $this->viewPath = 'shop';

        view()->share(compact('viewPath'));
    }
}
