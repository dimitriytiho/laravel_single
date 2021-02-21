<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать


    use SoftDeletes;


    public function __construct()
    {
        parent::__construct();

        $this->model = __CLASS__;
    }

    // Связь один ко многим внутри модели
    public function categories()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    // Связь многие ко многим
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }



    /**
     *
     * @return object
     *
     * Функция получения категории с товарами с учётом цены и фильтров.
     * Возвращает объект запроса.
     *
     * $slug - slug для категории.
     * $priceFrom - цена от.
     * $priceTo - цена до.
     * $filters - фильтры строкой в виде: slug1,slug2,
     */
    public static function getCategoryWithProductByFilter($slug, $priceFrom = null, $priceTo = null, $filters = null)
    {
        $self = new self();
        return $self->model::with(['products' => function ($query) use ($priceFrom, $priceTo, $filters) {

            // Товары с учётом цены
            if ($priceFrom && $priceTo) {
                $query->whereBetween('price', [$priceFrom, $priceTo]);
            }

            // Товары с учётом фильтров
            if ($filters) {
                $filters = rtrim($filters, ',');
                $filtersArr = explode(',', $filters);
                $query->whereHas('filters', function ($q) use ($filtersArr) {
                    $q->whereIn('slug', $filtersArr);
                });
            }
            $query->orderBy('sort')->orderBy('popular', 'desc');
        }])
            ->whereSlug($slug);
    }
}
