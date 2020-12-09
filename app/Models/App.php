<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;


    // Расширяем модель
    protected $class;
    protected $model;
    protected $table;
    protected $view;


    public function __construct()
    {
        parent::__construct();
    }


    /*
     * Scope для элементов с статусом active.
     *
     * Использование ->active()
     */
    public function scopeActive($query)
    {
        return $query->where('status', config('add.page_statuses')[1] ?: 'active');
    }


    /*
     * Добавляет в зарос связь из привязанной моделе.
     *
     * Использование ->withActiveSort('pages') - параметром передать название связи.
     *
     * Scope для привязанной таблицы, с условиями:
     * статус active,
     * сортировка по-сортировке,
     */
    public function scopeWithActiveSort($query, $type)
    {
        return $query->with([$type => function ($query) {
            $query
                ->where('status', config('add.page_statuses')[1] ?: 'active')
                ->orderBy('sort');
        }]);
    }


    // Записываем в БД популярность, т.е. прибавляем 1, когда пользователь открывает элемент, только один раз, используем сессию, вызов $values->savePopular;.
    protected function getSavePopularAttribute()
    {
        // Если в сессии нет текущего элемента, то прибавим популяность
        if (isset($this->slug) && isset($this->popular) && !session()->has('popular.' . $this->getTable() . '.' . $this->slug)) {

            $this->popular++;
            $this->save();

            // Записать в сессию страницу посещения на весь сеанс, чтобы каждый раз не прибавлять
            session()->put('popular.' . $this->getTable() . '.' . $this->slug, $this->popular);
        }
        return $this;
    }
}
