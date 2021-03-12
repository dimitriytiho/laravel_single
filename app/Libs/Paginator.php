<?php


namespace App\Libs;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Paginator extends Collection
{
    /**
     * Paginate a standard Laravel Collection.
     * @return object
     *
     * @param int $perPage
     * @param int $total
     * @param int $page
     * @param string $pageName
     *
     *
     * Из объекта Laravel создаём пагинацию, пример использования: $products = (new Paginator($values))->paginate($perPage); и пространство имён use App\Libs\Paginator;
     *
     * $perPage - кол-во элементов на странице, обязательный параметр.
     * $total - общее кол-во элементов, необязательный параметр.
     * $page - номер текущей страницы, необязательный параметр.
     * $pageName - название пагинации в Url, по-умолчанию page, необязательный параметр.
     *
     * https://gist.github.com/iamsajidjaved/4bd59517e4364ecec98436debdc51ecc
     */
    public function paginate($perPage, $total = null, $page = null, $pageName = 'page')
    {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
        return new LengthAwarePaginator(
            $this->forPage($page, $perPage),
            $total ?: $this->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
                'query' => request()->query(),
            ]
        );
    }
}
