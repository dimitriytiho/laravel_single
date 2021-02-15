<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать
    //protected $fillable = ['title', 'price', 'description'];  // Разрешается редактировать


    use SoftDeletes;



    // Связь многие ко многим
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }


    // Связь многие ко многим
    public function colors()
    {
        return $this->belongsToMany(Color::class)
            ->withPivot('id') // Добавить колонки в pivot
            ->withTimestamps(); // Добавить, чтобы записывать в БД created_at updated_at;
    }


    // Связь многие ко многим
    public function filters()
    {
        return $this->belongsToMany(Filter::class);
    }


    // Связь многие ко многим
    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }


    // Связь многие ко многим внутри модели
    public function related()
    {
        return $this->belongsToMany(self::class, 'product_related', 'related_product_id');
    }


    // Связь многие ко многим
    public function promos()
    {
        return $this->belongsToMany(Promo::class);
    }


    // Связь одим ко многим
    public function product_galleries()
    {
        return $this->hasMany(ProductGallery::class);
    }



    /**
     *
     *  @return string
     *
     * Возвращает слово: товар, товара или товаров, взависимости от передаваемого кол-ва.
     * $count - кол-во, т.е. цифра кол-во товаров.
     */
    public static function getWordProduct($count)
    {
        if ((int)$count) {

            // Возьмём последний символ
            $end = substr($count, -1, 1);

            if ($end == 1) {
                return 'товар';
            } elseif ($end > 1 && $end < 5) {
                return 'товара';
            }
        }
        return 'товаров';
    }
}
