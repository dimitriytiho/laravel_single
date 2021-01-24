<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\View;

class Menu extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь многие к одному
    public function menu_groups()
    {
        return $this->belongsTo(MenuGroup::class, 'belong_id', 'id');
    }

    // Связь один ко многим внутри модели
    public function menus()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }


    /**
     *
     * @return object
     *
     * Возвращает объкт меню.
     * Данные кэшируются.
     * $belongId - принимает id из таблицы menu_groups.
     * $cacheName - по-умолчанию не кэшируется, если надо кэшировать, то передать название кэша, необязательный параметр.
     */
    public static function get(int $belongId, $cacheName = '')
    {
        if ($belongId) {

            if ($cacheName && cache()->has($cacheName)) {
                $values = cache()->get($cacheName);

            } else {

                $values = self::where('belong_id', $belongId)
                    ->whereStatus(config('add.page_statuses')[1] ?? 'active')
                    ->orderBy('sort')
                    ->get();

                if ($cacheName) {
                    cache()->put($cacheName, $values);
                }
            }
            return $values;
        }
        return null;
    }


    /**
     *
     * @return array
     *
     * Возвращает массив дерево, где потомки в ключе child.
     * $menu - принимает id из таблицы menu_groups.
     * $cacheName - по-умолчанию не кэшируется, если надо кэшировать, то передать название кэша, необязательный параметр.
     */
    public static function tree(int $belongId, $cacheName = '')
    {
        $tree = [];
        if ($cacheName && cache()->has($cacheName)) {
            $tree = cache()->get($cacheName);

        } else {

            if ($belongId) {

                $menu = self::where('belong_id', $belongId)
                    ->whereStatus(config('add.page_statuses')[1] ?? 'active')
                    ->orderBy('sort')
                    ->get()
                    ->keyBy('id')
                    ->toArray();

                $tree = self::treeOfArr($menu);
            }

            if ($cacheName) {
                cache()->put($cacheName, $tree);
            }
        }
        return $tree;
    }


    /**
     *
     * @return array
     *
     * Возвращает массив дерево, где потомки в ключе child.
     * $arr - принимает массив, где id ключи массива.
     */
    public static function treeOfArr($arr)
    {
        $tree = [];
        if ($arr) {
            foreach ($arr as $id => &$node) {

                if (empty($node['parent_id'])) {
                    $tree[$id] = &$node;
                } else {
                    $arr[$node['parent_id']]['child'][$id] = &$node;
                }
            }
        }
        return $tree;
    }


    /**
     * @return string
     *
     * Возвращает вложенное меню с видом.
     * $viewName - название вида из папки resources/views/menu.
     * $tree - массив в виде дерева, его строит метод выше tree().
     * $tab - показывает вложенность, например передать -.
     * $cacheName - по-умолчанию не кэшируется, если надо кэшировать, то передать название кэша, необязательный параметр.
     */
    public static function getView($viewName, $tree, $tab = '', $cacheName = '')
    {
        $view = '';
        if ($cacheName && cache()->has($cacheName)) {
            $view = cache()->get($cacheName);

        } else {

            $i = 0;
            if ($tree && View::exists("menu.{$viewName}")) {
                foreach ($tree as $id => $item) {
                    $i++;
                    $view .= view("menu.{$viewName}", compact('viewName', 'item', 'tab', 'id', 'i'))->render();
                }
            }

            if ($cacheName) {
                cache()->put($cacheName, $view);
            }
        }
        return $view;
    }
}
