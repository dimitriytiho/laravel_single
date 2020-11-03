<?php

namespace App\Exports;

use App\Helpers\ImportExport;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromQuery, WithHeadings // FromCollection
{
    // Уникальная колонка в таблице
    public $unique = 'email';

    // Перечислить колонки для работы экспорта и импорта, где 1 - это обязательное поле.
    public $columns = [
        'id' => 0,
        'name' => 1,
        'email' => 1,
        'tel' => 1,
        'address' => 0,
        'status' => 0,
        'note' => 0,
        'ip' => 0,
        'img' => 0,
        //'accept' => 0,
        //'email_verified_at' => 0,
        //'remember_token' => 0,
        //'password' => 1,
        //'created_at' => 0,
        //'updated_at' => 0,
    ];

    public function query()
    {
        return User::select(ImportExport::arrColumns($this->columns));
    }

    public function headings(): array
    {
        return ImportExport::arrColumns($this->columns);
    }


    /*public function collection()
    {
        return User::all();
    }*/
}
