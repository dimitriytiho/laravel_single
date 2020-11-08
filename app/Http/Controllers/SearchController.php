<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Models\Main;

class SearchController extends AppController
{
    // Pages используется по-умолчанию
    private $tableSearch = 'pages';

    // Page используется по-умолчанию
    private $routeSearch = 'page';



    public function __construct(Request $request)
    {
        parent::__construct();

        $class = $this->class = str_replace('Controller', '', class_basename(__CLASS__));
        $c = $this->c = Str::lower($this->class);
        $view = $this->view = Str::snake($this->c);

        view()->share(compact('class', 'c', 'view'));
    }


    public function index(Request $request)
    {
        $query = s($request->query('s'));
        $values = null;

        if ($query) {
            Main::set('search_query', $query);


            // Если используется несколько таблиц, то добавить SQL запрос
            /*$unionProducts = DB::table('products')
                ->select([DB::raw("'product'"), 'id', 'title', 'slug'])
                ->where('status', $this->statusActive)
                ->where('title', 'LIKE', "%{$query}%");*/


            $values = DB::table($this->tableSearch)

                // Если используется несколько таблиц, то добавить эту строку
                //->union($unionProducts)




                ->select([DB::raw("'{$this->routeSearch}' as route"), 'id', 'title', 'slug'])
                ->where('status', $this->statusActive)
                ->where('title', 'LIKE', "%{$query}%")
                ->paginate($this->perPage);
        }

        $title = __('a.search');
        return view("page.{$this->view}", compact('title', 'values'));
    }


    public function js(Request $request)
    {
        if ($request->ajax()) {
            $query = s($request->get('query'));

            if ($query) {

                // Если используется несколько таблиц, то добавить SQL запрос
                /*$unionProducts = DB::table('products')
                    ->select([DB::raw("'product'"), 'id', 'title', 'slug'])
                    ->where('status', $this->statusActive)
                    ->where('title', 'LIKE', "%{$query}%");*/


                $values = DB::table($this->tableSearch)

                    // Если используется несколько таблиц, то добавить эту строку
                    //->union($unionProducts)



                    ->select([DB::raw("'{$this->routeSearch}' as route"), 'id', 'title', 'slug'])
                    ->where('status', $this->statusActive)
                    ->where('title', 'LIKE', "%{$query}%")
                    ->limit('10')
                    ->get();
                return $values->toJson();
            }
            die;
        }
        Main::getError('Request', __METHOD__);
    }
}

