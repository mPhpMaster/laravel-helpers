<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Traits;

use Illuminate\Support\Str;

/**
 * Trait TQueryFilter
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static queryFilter() TQueryFilter::queryFilter()
 *
 * @see TQueryFilter::scopeQueryFilter()
 *
 * @package MPhpMaster\LaravelHelpers\Traits
 */
trait TQueryFilter
{
    /**
     * Returns request param name to get keyword.
     *
     * @return string|null
     */
    abstract public static function getQueryFilterParam();

    /**
     * Returns array of allowed columns and query operator
     *
     * @param mixed ...$columns
     *
     * @return array Example: [ ['id', '='] ]
     */
    public static function getQueryFilterColumns(...$columns): array
    {
        return array_filter($columns);
    }

    /**
     * @param mixed       $key
     * @param mixed       $value
     * @param string|null $datatype
     *
     * @return array
     */
    abstract public static function isColumnValidForDatatype($key, $value, $datatype = null);

    /**
     * @param mixed       $column_name
     * @param mixed       $value
     * @param string|null $datatype
     *
     * @return bool
     */
    public static function testColumnValidation($column_name, $value, $datatype = null)
    {
        $data = static::isColumnValidForDatatype($column_name, $value, $datatype);
        $result = true;
        foreach ($data as $k => $v) {
            [$col, $_datatype] = is_array($v) ? $v : [$v, null];
            if ( is_string($col) ? $col == $column_name : getValue($col, $column_name, $value) ) {
                $result = $datatype == getValue($_datatype, $column_name, $value);
                break;
            }
        }

        return $result;
    }

    /**
     * Returns keyword from request by static::getQueryFilterParam()
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Closure|mixed|null
     */
    public static function getQueryFilterKeywordFromRequest(\Illuminate\Http\Request $request)
    {
        if ( $param = static::getQueryFilterParam() ) {
            /** @var \Illuminate\Http\Request $_request */
            $_request = $request ?? request();
            return $_request->get($param, null);
        }

        return null;
    }

    /**
     * Apply where or whatever to builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $column_name
     * @param string|\Closure|mixed                 $value
     * @param string|\Closure|null                  $operator
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @noinspection ForgottenDebugOutputInspection
     */
    public static function applyQueryFilterToBuilder($query, $column_name, $value, $operator = null)
    {
//        ($column_name === 'demand_date') && dE(
//            static::isColumnValidForDatatype($column_name, $value, get_type($value)),
//            array($column_name, $value, get_type($value)),
//            [$column_name, $value, $operator]
//        );
        if ( !static::isColumnValidForDatatype($column_name, $value, get_type($value)) ) {
            return $query;
        }

        if ( isClosure($value) ) {
            return $query->orWhere(function ($q) use ($value, $column_name) {
                call_user_func_array($value, [
                    $q, $column_name
                ]);
            });
//            $value = call_user_func_array($value, [$query, $key]);
//            return $value;
        }

        if ( isClosure($operator) ) {
            return $query->orWhere(function ($q) use ($query, $value, $column_name, $operator) {
                call_user_func_array($operator, [
                    $q, $value, $column_name
                ]);
            });
//            return call_user_func_array($operator, [
//                $query, $value, $key
//            ]);
        }
        $operator = $operator ?? '=';
        $methodName = Str::studly("by_{$column_name}");

//        request()->has('ddd') && dump($methodName);

        if ( method_exists($query, $methodName) && is_callable([$query, $methodName]) ) {
            $query->orWhere(function ($q) use ($methodName, $value, $column_name) {
                $q->{$methodName}($value, $column_name);
            });
        } else {
            $query = $query->orWhere($column_name, $operator, $value);
        }

        return $query;
    }

    /**
     * Apply filter by query & keyword
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed|null                            $keyword
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyQueryFilter($query, $keyword = null)
    {
        if ( !is_null($keyword) ) {
            toCollect(static::getQueryFilterColumns())->each(function ($col, $k) use ($query, &$keyword) {
                $col = is_array($col) ? $col : [$col, '='];
                $operator = last($col);
                $col = head($col);

                static::applyQueryFilterToBuilder($query, $col, $keyword, $operator);
            });
        }
        request()->has('ddd') && dE($query->sql(), $query->sql2());

        return $query->latest();
//        (
//            (`username` like "%Ahmed%") or
//            (`first_name` like "%Ahmed%") or
//            (`last_name` like "%Ahmed%") or
//            (`school_name` like "%Ahmed%") or
//            (`mobile` = "05Ahmed") or
//            `active` = "Ahmed"
//        )
    }

    /**
     * **For Models**
     * self::queryFilter()
     * Apply QueryFilter.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|mixed|null                     $keyword
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQueryFilter($query, $keyword = null)
    {
        $keyword = $keyword ?? app()->call([$this, 'getQueryFilterKeywordFromRequest']);

        return static::applyQueryFilter($query, $keyword);
    }
}
