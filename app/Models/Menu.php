<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * $belongId - принимает belong_id из таблицы menu_names.
     */
    public static function get(int $belongId)
    {
        if ($belongId) {
            $name = "menu_names_{$belongId}";

            if (cache()->has($name)) {
                $values = cache()->get($name);

            } else {

                $values = self::where('belong_id', $belongId)->get();
                cache()->put($name, $values);
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
     * $menu - принимает коллекцию Laravel (например: Menu::where('belong_id', 1)->get()).
     * $nameParentId - строим дерево по полю parent_id, если нужно изменить, то передайте, необязательный параметр.
     */
    public static function tree($menu, $nameParentId = 'parent_id')
    {
        $tree = [];
        if (is_object($menu) && $menu->count()) {
            $menu = $menu->keyBy('id')->toArray();

            foreach ($menu as $id => &$node) {

                if (empty($node[$nameParentId])) {
                    $tree[$id] = &$node;
                } else {
                    $menu[$node[$nameParentId]]['child'][$id] = &$node;
                }
            }
        }

        return $tree;
    }
}
