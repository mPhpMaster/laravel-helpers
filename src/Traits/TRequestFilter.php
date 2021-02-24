<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

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
                try {
                    $query = $query->{$methodName}($v, $k);

                    return;
                } catch (\BadMethodCallException $exception) {

                }
            }

            $query = $query->where($k, $v);
        });

        return $query->latest();
    }

    /**
     * **For Models**
     * self::requestFilter()
     * Scope a query to append query with where from request.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @noinspection ForgottenDebugOutputInspection
     */
    public function _scopeRequestFilter($query)
    {
        $filterBy = self::getFiltersFromRequest();
        toCollect($filterBy)->each(function ($v, $k) use (&$query) {
            $methodNames = [
                "by_{$k}"
            ];
            $methodName = null;
            foreach ($methodNames as $_methodName) {
                $_methodName = Str::studly($_methodName);

                if ( $query->hasNamedScope($_methodName) || is_callable([$query, $_methodName]) ) {
                    $methodName = $_methodName;
                    break;
                }
            }

            if ( $methodName && is_callable([$query, $methodName]) ) {
                try {
                    $query = $query->{$methodName}($v, $k);

                    return;
                } catch (\BadMethodCallException $exception) {

                }
            }

            $query = $query->where($k, $v);
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
