<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends AppController
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
        $model = $this->model = "{$this->namespaceModels}\\{$class}";
        $table = $this->table = with(new $model)->getTable();
        $view = $this->view = Str::snake($this->c);

        view()->share(compact('class', 'c', 'model', 'table', 'view'));
    }


    public function index()
    {
        $values = $this->model::where('user_id', auth()->user()->id)
            ->paginate($this->perPage);

        $f = Str::snake(__FUNCTION__);
        $title = 'История заказов';

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->add([[route('home.order_index') => $title]])
            ->add([[route('home.index') => 'account']])
            ->get();

        return view("{$this->viewPath}.{$this->view}_{$f}", compact('title', 'breadcrumbs', 'values'));
    }


    public function show($id)
    {
        $values = $this->model::where('user_id', auth()->user()->id)
            ->findOrFail($id);
        $products = $values->products->count() ? $values->products->keyBy('id')->toArray() : [];

        $f = Str::snake(__FUNCTION__);
        $title = __('a.order') . ' ' . $id;

        // Хлебные крошки
        $breadcrumbs = $this->breadcrumbs
            ->add([[route('home.order_show', $id) => $title]])
            ->add([[route('home.order_index') => 'История заказов']])
            ->add([[route('home.index') => 'account']])
            ->get();

        return view("{$this->viewPath}.{$this->view}_{$f}", compact('title', 'breadcrumbs', 'values', 'products'));
    }
}
