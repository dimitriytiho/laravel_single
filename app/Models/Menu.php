<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends App
{
    protected $table = 'menu';
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    // Связь многие к одному
    public function menuName()
    {
        return $this->belongsTo(MenuName::class, 'belong_id', 'id');
    }

    // Связь один ко многим внутри модели
    public function parents()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
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
