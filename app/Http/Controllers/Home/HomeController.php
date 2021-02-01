<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

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
    public function index(Request $request)
    {
        $f = Str::snake(__FUNCTION__);
        $ip = $request->ip();
        /*$d = date('d');
        $m = __('s.' . date('F'));
        $y = date('Y');
        $l = __('s.' . date('l'));
        $date = "{$d} {$m} {$y}, {$l}";*/

        $dt = Carbon::now();
        $date = $dt->translatedFormat('d F Y, l');
        $time = date('H:i:s');

        $agent = $request->server('HTTP_USER_AGENT');
        $accept = $request->server('HTTP_ACCEPT');
        $title = __('s.account');

        return view("{$this->viewPath}.{$this->view}_{$f}", compact('title', 'ip', 'date', 'time', 'agent', 'accept'));
    }
}
