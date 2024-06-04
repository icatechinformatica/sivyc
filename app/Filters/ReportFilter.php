<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ReportFilter
{
    protected $filters = [];

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function apply(Builder $query): Builder
    {
        foreach ($this->filters as $filter => $value) {
            if ($filter instanceof FilterInterface) {
                $query = $filter->apply($query);
            }
        }

        return $query;
    }
}
