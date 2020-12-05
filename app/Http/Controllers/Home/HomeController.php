<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends AppController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $view = $this->view = Str::snake($this->c);

        view()->share(compact('class', 'c', 'view'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $f = Str::snake(__FUNCTION__);
        return view("{$this->viewPath}.{$this->view}_{$f}");
    }
}
