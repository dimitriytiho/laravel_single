<?php

namespace App\Exports;

use App\Helpers\ImportExport;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriesExport  implements FromQuery, WithHeadings // FromCollection
{
    // Уникальная колонка в таблице
    public $unique = 'slug';

    // Перечислить колонки для работы экспорта и импорта, где 1 - это обязательное поле.
    public $columns = [
        'id' => 0,
        'parent_id' => 0,
        'title' => 1,
        'slug' => 1,
        'status' => 0,
        'sort' => 0,
        'description' => 0,
        //'body' => 0,
        //'created_at' => 0,
        //'updated_at' => 0,
    ];

    public function query()
    {
        return Category::select(ImportExport::arrColumns($this->columns));
    }

    public function headings(): array
    {
        return ImportExport::arrColumns($this->columns);
    }


    /*public function collection()
    {
        return Category::all();
    }*/

}
