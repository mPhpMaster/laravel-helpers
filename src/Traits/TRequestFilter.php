<?php
namespace mPhpMaster\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

/**
 * Trait TRequestFilter
 *
 * @method static \Illuminate\Database\Eloquent\Builder requestFilter() RequestFilter::requestFilter()
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
     * @return array
     */
    public static function allowRequestFilters()
    {
        return [];
    }

    /**
     * Returns array of filter pair
     *      [   `column name` =>  `column value`    ]
     *
     * @return array
     */
    public static function getFiltersFromRequest()
    {
        return request()->only(self::allowRequestFilters());
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
        toCollect($filterBy)->each(function ($k, $v) use (&$query) {
            $query = $query->where($k, $v);
        });

        return $query;
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

        return $query;
    }

    /**
     * @param $tables
     * @param $column
     *
     * @return string|null
     */
    private static function getConcatedTableForColumn($tables, $column)
    {
        $tables = (array)$tables;
        if ( is_array($tables) && count($tables) == 1 && key($tables) == 0 ) {
            $tables = ['*' => head($tables)];
        }

        $table = null;
        if ( array_key_exists($column, $tables) ) {
            $table = array_get($tables, $column, null);
        } else if ( array_key_exists('*', $tables) ) {
            $table = array_get($tables, '*', null);
        }

        return $table ? "{$table}.{$column}" : $column;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|Relation|\Illuminate\Database\Eloquent\Model $query
     * @param array|Request|null                                                                 $tables
     * @param \Illuminate\Http\Request|null                                                      $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyCustomFilter($query, $tables = null, Request $request = null)
    {
        if ( $tables instanceof \Illuminate\Http\Request ) {
            $request = $tables;
            $tables = [];
        }

        $request = $request ?: request();
        $model = $query;
        $inputs = collect();
        $column = function ($column) use ($tables) {
            return static::getConcatedTableForColumn($tables, $column);
        };

        /**
         * Filler by country
         */
        $model->when(getFirstValueByKey($request, ['country_id', 'country'], false), function ($model, $country_id)
        use (&$inputs, $column) {
            $inputs->put('country_id', $country_id);
            return $model->where($column('country_id'), $country_id);
        })
            // Filler by region
            ->when(getFirstValueByKey($request, ['city_id', 'city', 'region', 'region_id'], false), function ($model, $region_id)
            use (&$inputs, $column) {
                $inputs->put('region_id', $region_id);
                return $model->where($column('region_id'), $region_id);
            })
            // Filler by day
            ->when(getFirstValueByKey($request, ['day_id', 'day'], false), function ($model, $day_id) use (&$inputs, $column) {
                $inputs->put('day_id', $day_id);
                return $model->whereHas('worktimes', function (Builder $q) use ($day_id) {
                    return $q->whereIn('day_id', explode(',', $day_id));
                });
            })
            // Filler by time from; todo: apply filter M1
            ->when(getFirstKeyByKey($request, ['start_time', 'time_from'], false), function ($model) use ($request, &$inputs, $column) {
                $start_time = getFirstValueByKey($request, ['start_time', 'time_from'], false);
                $inputs->put('start_time', $start_time);
                return $model->whereHas('worktimes', function (Builder $q) use ($start_time) {
                    return $q->whereTime('start_time', ">=", carbon()->now()->setTime($start_time, "00")->format("H"));
                });
            })
            // Filler by time to; todo: apply filter M1
            ->when(getFirstKeyByKey($request, ['end_time', 'time_to'], false), function ($model) use ($request, &$inputs, $column) {
                $end_time = getFirstValueByKey($request, ['end_time', 'time_to'], false);
                $inputs->put('end_time', $end_time);
                return $model->whereHas('worktimes', function (Builder $q) use ($end_time) {
                    return $q->whereTime('end_time', "<=", "$end_time:00");
                });
            })
            // Filler by name;
            ->when(getFirstValueByKey($request, ['name'], false), function ($model, $name) use ($request, &$inputs, $column) {
                $inputs->put('name', $name);
                return $model->where($column('name'), 'like', "%{$name}%");
            })
        ;
//        $inputs->dd();
        return $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|Relation|\Illuminate\Database\Eloquent\Model $query
     * @param array|Request|null                                                                 $tables
     * @param \Illuminate\Http\Request|null                                                      $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyNameFilter($query, $tables = null, Request $request = null)
    {
        if ( $tables instanceof \Illuminate\Http\Request ) {
            $request = $tables;
            $tables = [];
        }

        $request = $request ?: request();
        $model = $query;
        $inputs = collect();
        $column = function ($column) use ($tables) {
            return static::getConcatedTableForColumn($tables, $column);
        };

        /**
         * Filler by country
         */
        return $model->when(getFirstValueByKey($request, ['name'], false), function ($model, $name)
        use (&$inputs, $column) {
            $inputs->put('name', $name);
            return $model->where($column('name'), 'like', "%{$name}%");
        })
            // Filler by name_ar, name_en;
            ->when(getFirstValueByKey($request, ['names', 'name_ar', 'name_en'], false), function ($model, $name)
            use ($request, &$inputs) {
                $names = [];

                if($name_ar = $request->get('name_ar', false)) {
                    $inputs->put('name_ar', $name_ar);
                    $names[] = ['name_ar', 'like', "%{$name_ar}%", 'or'];
                }

                if($name_en = $request->get('name_en', false)) {
                    $inputs->put('name_en', $name_en);
                    $names[] = ['name_en', 'like', "%{$name_en}%", 'or'];
                }

                if($allNames = $request->get('names', false)) {
                    $inputs->put('names', $allNames);
                    $names[] = ['name_ar', 'like', "%{$allNames}%", 'or'];
                    $names[] = ['name_en', 'like', "%{$allNames}%", 'or'];
                }

                return count($names) ? $model->where($names) : $model;
            });
    }
}
