<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductsImport implements ToModel
{
    public function model(array $row)
    {
        return new Product([
            //
        ]);

        /*return new Product([
            'title' => $row[1],
            'slug' => $row[2],
            'status' => $row[3],
            'price' => $row[4],
            'old_price' => $row[5],
            'sort' => $row[6],
            'description' => $row[7],
            //'body' => $row[8],
            //'img' => $row[9],
        ]);*/
    }
}
