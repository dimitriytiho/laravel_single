<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Main;

class SearchController extends AppController
{
    // Pages используется по-умолчанию
    private $tableSearch = 'products';

    // Page используется по-умолчанию
    private $routeSearch = 'product';



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
                ->whereStatus($this->statusActive)
                ->where('title', 'LIKE', "%{$query}%");*/


            $values = DB::table($this->tableSearch)

                // Если используется несколько таблиц, то добавить эту строку
                //->union($unionProducts)


                ->whereNull('deleted_at')
                ->whereStatus($this->statusActive)
                ->where('title', 'like', "%{$query}%")
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
                    ->whereStatus($this->statusActive)
                    ->where('title', 'LIKE', "%{$query}%");*/


                $values = DB::table($this->tableSearch)

                    // Если используется несколько таблиц, то добавить эту строку
                    //->union($unionProducts)



                    ->select('id', 'title', 'slug', 'img')
                    ->addSelect(DB::raw("'{$this->routeSearch}' as route"))
                    ->whereNull('deleted_at')
                    ->whereStatus($this->statusActive)
                    ->where('title', 'like', "%{$query}%")
                    ->limit('10')
                    ->get();

                if ($values->count()) {
                    $res = view('inc.search_item', compact('values'))->render();
                }

                return $res ?? '';
            }
            die;
        }
        Main::getError('Request', __METHOD__);
    }
}
