<?php

namespace App\Exports;

use App\Helpers\ImportExport;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductsExport implements FromQuery, WithHeadings //FromCollection, WithMapping, WithColumnFormatting
{
    // Уникальная колонка в таблице
    public $unique = 'slug';

    // Перечислить колонки для работы экспорта и импорта, где 1 - это обязательное поле.
    public $columns = [
        'id' => 0,
        'title' => 1,
        'slug' => 1,
        'status' => 0,
        'price' => 0,
        'old_price' => 0,
        'sort' => 0,
        'description' => 0,
        //'body' => 0,
        'img' => 0,
        //'created_at' => 0,
        //'updated_at' => 0,
    ];


    /*public function collection()
    {
        return Product::select('id', 'title', 'slug', 'status', 'price', 'old_price', 'sort', 'description')->get();
        //return Product::all();
    }*/


    public function query()
    {
        return Product::select(
            ImportExport::arrColumns($this->columns)
            /*'id',
            'title',
            'slug',
            'status',
            'price',
            'old_price',
            'sort',
            'description',
            //'body',
            'img'*/
            //'created_at',
            //'updated_at'
        );
    }

    public function headings(): array
    {
        return ImportExport::arrColumns($this->columns);
        /*return [
            'id',
            'title',
            'slug',
            'status',
            'price',
            'old_price',
            'sort',
            'description',
            //'body',
            'img',
            //'created_at',
            //'updated_at',
        ];*/
    }


    /*public function map($invoice): array
    {
        return [
            $invoice->id,
            $invoice->title,
            $invoice->slug,
            $invoice->status,
            $invoice->price,
            $invoice->old_price,
            $invoice->sort,
            $invoice->description,
            //$invoice->body,
            $invoice->img,
            Date::dateTimeToExcel($invoice->created_at),
            Date::dateTimeToExcel($invoice->updated_at)

            //$invoice->invoice_number,
            //Date::dateTimeToExcel($invoice->created_at),
            //$invoice->total
        ];
    }*/


    /*public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            //'C' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
    }*/
}
