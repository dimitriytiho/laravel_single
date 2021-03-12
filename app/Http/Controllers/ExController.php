<?php

namespace App\Http\Controllers;

use App\Helpers\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Schema};

class ExController extends Controller
{
    /*
     * Возвращает данные в json.
     * $table - указать название таблицы, после передачи данные удалите название.
     */
    public function json(Request $request)
    {
        $table = ''; // users


        $ex = null;
        if ($table && Schema::hasTable($table)) {
            $ex = DB::table($table)->get();
        }

        return $ex;
    }


    /*
     * Сохраняет данные из json, полученые из url.
     * $table - указать название таблицы, после сохранения данные удалите название.
     * $url - указать url, c которого получить данные, после сохранения данные удалите url.
     */
    public function get(Request $request)
    {
        $table = ''; // users
        $url = ''; // https://omegakontur.ru/ex/json


        // Получаем данные
        if ($table && $url) {
            $data = File::getDataFromUrl($url);
            $json = json_decode($data);

            // Превращаем данные в массив
            $arr = [];
            if ($json) {
                foreach ($json as $item) {
                    $item = (array)$item;

                    // Если нужно удалить
                    //unset($item['role_id']);

                    $arr[] = $item;
                }
            }


            // Сохраняем данные
            $res = Schema::hasTable($table) ? DB::table($table)->insert($arr) : false;
            dump($res);
        }

        return null;
    }
}
