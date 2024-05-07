<?php

namespace App\Models\Traits;

use Arr;

trait Filterable
{
    protected $filterSeparator = ':';

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['keyword'])) {
            $query->search($filters['keyword']);
        }

        $query->filterByFields($filters);
    }

    public function scopeSearch($query, $keyword)
    {
        $filterable = isset($this->filterable) ? $this->filterable : [];

        $query->where(function ($query) use ($filterable, $keyword) {
            foreach ($filterable as $field) {
                // Look for relationship in the field name (separated by colon)
                $explodes = explode($this->filterSeparator, $field);

                if (isset($explodes[1])) {
                    $relField = $explodes[1];
                    $query->orWhereHas($explodes[0], function ($query) use ($relField, $keyword) {
                        $query->where($relField, 'LIKE', "%{$keyword}%");
                    });
                } else {
                    $query->orWhere($field, 'LIKE', "%{$keyword}%");
                }
            }
        });
    }

    public function scopeFilterByFields($query, $filters)
    {
        $filterable = isset($this->filterable) ? $this->filterable : [];
        $filters = Arr::only($filters, $filterable);

        foreach ($filters as $field => $keyword) {
            // Look for relationship in the field name (separated by colon)
            $explodes = explode($this->filterSeparator, $field);

            if (isset($explodes[1])) {
                $relField = $explodes[1];
                $query->whereHas($explodes[0], function ($query) use ($relField, $keyword) {
                    $query->where($relField, $keyword);
                });
            } else {
                if (in_array($field, ['created_at', 'updated_at'])) {
                    $query->whereDate($field, $keyword);
                } else {
                    $query->where($field, $keyword);
                }
            }
        }
    }
}
