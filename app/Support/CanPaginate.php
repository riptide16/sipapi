<?php

namespace App\Support;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

trait CanPaginate
{
    public function simplePaginate($items, $perPage = 20, $pageName = 'page', $page = null)
    {
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $page = $page ?? Paginator::resolveCurrentPage($pageName);
        $items = $this->sliceItems($items, $page);

        return new Paginator($items, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    protected function sliceItems($items, $currentPage)
    {
        return $items->slice($currentPage - 1);
    }
}
