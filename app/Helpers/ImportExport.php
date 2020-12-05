<?php


namespace App\Helpers;


use App\Exports\ProductsExport;
use App\Helpers\Admin\App;

class ImportExport
{
    private $columns;


    private function __construct()
    {
        $columns = new ProductsExport();
        $this->columns = $columns->columns;
    }


    /**
     *
     * @return array
     *
     * Возвращает массив названий колонок из таблицы.
     * $arrColumns - массив в таком формате:
        $arrColumns = [
            'title' => 0,
            'slug' => 1,
        ];
        где 1 - это обязательное поле.
        По-умолчанию он берётся из модели \App\Helpers\ProductsExport, необязательное поле.
     */
    public static function arrColumns($arrColumns) {

        /*$self = new self();
        $arrColumns = $self->columns;*/

        if ($arrColumns) {
            $key = 0;
            foreach ($arrColumns as $value => $required) {

                $arr[$key] = $value;
                $key++;
            }
            return $arr;
        }
        return [];
    }


    /*
     * Возвращает массив в таком формате:
     $insertData[] = [
            'title' => $row[0],
            'alias' => $row[1],
        ];
     * $row - ряд из цикла, принятого файла эксель.
     * $arrColumns - массив в таком формате:
        $arrColumns = [
            'title' => 0,
            'slug' => 1,
        ];
        где 1 - это обязательное поле.
        По-умолчанию он берётся из модели \App\Helpers\ProductsExport, необязательное поле.
     */
    public static function arrColumnsRequired($row, $arrColumns = [], $date = true, $slugName = null, $titleKey = null) {

        /*$self = new self();
        $intColumns = $self->intColumns;*/

        if ($row && $arrColumns) {
            $key = 0;
            foreach ($arrColumns as $value => $required) {

                // Если поле Slug и его нет, то сделаем его из title
                if ($slugName && $titleKey && $slugName === $value && !$row[$key]) {
                    $row[$key] = App::cyrillicToLatin($row[$titleKey]);
                }

                // Пропускаем если обязательного поля нет
                if ($required && !$row[$key]) {
                    return $value;
                }

                /*if (!$row[$key] && in_array($value, $intColumns)) {
                    $row[$key] = '0';
                }*/

                $arr[$value] = $row[$key];
                $key++;
            }
            if ($date) {
                $arr['created_at'] = date('Y-m-d H:i:s');
                $arr['updated_at'] = date('Y-m-d H:i:s');
            }
            return $arr;
        }
        return [];
    }


    /**
     *
     * @return string
     *
     * Возвращает строку с названиями колонок.
     * $arrColumns - массив в таком формате:
        $arrColumns = [
            'title' => 0,
            'slug' => 1,
        ];
        где 1 - это обязательное поле.
        По-умолчанию он берётся из модели \App\Helpers\ProductsExport, необязательное поле.
     */
    public static function strColumns($arrColumns)
    {
        if ($arrColumns) {
            $str = '';
            foreach ($arrColumns as $value => $required) {

                $str .= "'{$value}',";
            }
            $str = rtrim($str, ',');
            return $str;
        }
        return '';
    }
}
