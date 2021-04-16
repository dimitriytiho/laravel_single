<?php


namespace App\Traits;


trait TModelScopes
{
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
     * Добавляет в запрос связь из привязанной моделе.
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


    /*
     * Проверить в scope: сейчас попадает ли в промежуток времени.
     *
     * Использование ->betweenTime()
     */
    public function scopeBetweenTime($query)
    {
        $now = date('Y-m-d h:i:s');
        return $query
            ->where('start', '<', $now)
            ->where('end', '>', $now);
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
