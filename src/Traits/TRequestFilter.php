<?php

namespace mPhpMaster\Support\Traits;

use Illuminate\Support\Str;

/**
 * Trait TRequestFilter
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static requestFilter() TRequestFilter::requestFilter()
 *
 * @see TRequestFilter::scopeRequestFilter()
 *
 * @package mPhpMaster\Support\Traits
 */
trait TRequestFilter
{
    /**
     * Returns array of allowed keys to filter with
     *
     * @return array|null null for all
     */
    public static function allowRequestFilters()
    {
        return null;
    }

    /**
     * Returns array of filter pair
     *      [   `column name` =>  `column value`    ]
     *
     * @return array
     */
    public static function getFiltersFromRequest()
    {
        $allowed = self::allowRequestFilters();
        return is_null($allowed) ? request()->all() : request()->only($allowed);
    }

    /**
     * **For Models**
     * self::requestFilter()
     * Scope a query to append query with where from request.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequestFilter($query)
    {
        $filterBy = self::getFiltersFromRequest();
        toCollect($filterBy)->each(function ($v, $k) use (&$query) {
            $methodName = Str::studly("by_{$k}");

            if ( is_callable([$query, $methodName]) ) {
                $query = $query->{$methodName}($v, $k);
            } else {
                $query = $query->where($k, $v);
            }
        });

        return $query->latest();
    }

    /**
     * **For Controllers**
     * self::queryRequestFilter()
     * Scope a query to append query with where from request.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyRequestFilter($query)
    {
        $filterBy = self::getFiltersFromRequest();
        toCollect($filterBy)->each(function ($k, $v) use (&$query) {
            $query = $query->where($k, $v);
        });

        return $query->latest();
    }
}
