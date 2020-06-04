<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if (!function_exists('array_keys_exists')) {
    /**
     * Easily check if multiple array keys exist.
     *
     * @param array $keys
     * @param array $arr
     *
     * @return boolean
     */
    function array_keys_exists(array $keys, array $arr)
    {
        return !array_diff_key(array_flip($keys), $arr);
    }
}

/**
 * return bool
 */
if (!function_exists('isArrayable')) {
    /**
     * Check if the given var is Arrayable (has ->toArray()).
     *
     * @param mixed|null $array
     *
     * @return bool
     */
    function isArrayable($array): bool
    {
        return $array instanceof \Illuminate\Contracts\Support\Arrayable || method_exists($array, 'toArray');
    }
}

/**
 * return bool
 */
if (!function_exists('isArrayableOrArray')) {
    /**
     * Check if the given var is Array | is Arrayable (has ->toArray()).
     *
     * @param mixed|null $array
     *
     * @return bool
     */
    function isArrayableOrArray($array): bool
    {
        return is_array($array) || isArrayable($array);
    }
}

/**
 * return bool
 */
if (!function_exists('isAllable')) {
    /**
     * Check if the given var is Allable (has ->all()).
     *
     * @param array|\Illuminate\Contracts\Support\Arrayable|\Illuminate\Support\Collection|mixed $array
     *
     * @return bool
     */
    function isAllable($array): bool
    {
        return method_exists($array, 'all');
    }
}

/**
 * return bool
 */
if (!function_exists('isPaginator')) {
    /**
     * Check if the given var is paginator instance.
     *
     * @param $value
     *
     * @return bool
     */
    function isPaginator($value): bool
    {
        return (
            $value instanceof \Illuminate\Pagination\LengthAwarePaginator ||
            $value instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ||
            $value instanceof \Illuminate\Pagination\Paginator ||
            $value instanceof \Illuminate\Contracts\Pagination\Paginator ||
            $value instanceof \Illuminate\Pagination\AbstractPaginator ||

            (class_exists($class = "League\Fractal\Pagination\PaginatorInterface") && $value instanceof $class) ||
            (class_exists($class = "League\Fractal\Pagination\PaginatorInterface") && $value instanceof $class) ||
            (class_exists($class = "League\Fractal\Pagination\IlluminatePaginatorAdapter") && $value instanceof $class) ||
            (class_exists($class = "League\Fractal\Pagination\PagerfantaPaginatorAdapter") && $value instanceof $class) ||
            (class_exists($class = "League\Fractal\Pagination\DoctrinePaginatorAdapter") && $value instanceof $class)
        );
    }
}

/**
 * return bool
 */
if (!function_exists('isPaginated')) {
    /**
     * Check if the given var is paginate result.
     *
     * @param $value
     *
     * @return null
     */
    function isPaginated($value)
    {
        if (isPaginator($value) || method_exists($value, 'getCollection')) {
            return true;
        }

        if (is_array($value)) {
            if (
                Arr::has($value, ['current_page', 'per_page']) ||
                Arr::has($value, 'meta')
            ) {
                return true;
            }
        }

        return false;
    }
}

/**
 * return mixed
 */
if (!function_exists('isConsole')) {
    /**
     *
     * ### Check if the application running in `Console (CLI)`.
     * *Return custom response by checking __App::runningInConsole()__ method.*
     *
     * ---
     * --|| **Basically the return is one of two variables.**
     *
     * -----| **$runningInConsole** By default its `true`, Returns this **ONLY** If App. is in **Console**.
     *
     * -----| **$notRunningInConsole** By default its `false`, Returns this **ONLY** If App. is **NOT** in **Console**.
     *
     * @param mixed $runningInConsole | return value of ( $runningInConsole ) when App is running in console.
     * @param mixed $notRunningInConsole | return value of ( $notRunningInConsole ) when App is NOT running in console.
     *
     * @return mixed
     */
    function isConsole($runningInConsole = true, $notRunningInConsole = false)
    {
        return App::runningInConsole() ? $runningInConsole : $notRunningInConsole;
    }
}

/**
 * return bool
 */
if (!function_exists('isBuilder')) {
    /**
     * ### Check if the given var is Query Builder | Eloquent Builder | Relation.
     *
     * @param \Illuminate\Database\Query\Builder|Builder|Relation|mixed $var | return $var === QueryBuilder.
     *
     * @return bool
     */
    function isBuilder($var): bool
    {
        return $var instanceof \Illuminate\Database\Query\Builder || $var instanceof Builder || $var instanceof Relation;
    }
}

/**
 * return bool
 */
if (!function_exists('isLoggedIn')) {
    /**
     * ### Check if user has logged in.
     *
     * @return bool
     */
    function isLoggedIn(): bool
    {
        return !!Auth()->check();
    }
}

/**
 * return bool
 */
if (!function_exists('isGuest')) {
    /**
     * ### Check if user is guest.
     *
     * @return bool
     */
    function isGuest(): bool
    {
        return !!Auth()->guest();
    }
}

if (!function_exists('endsWithAny')) {
    /**
     * Determine if a given string ends with a given substrings then return substring or False when fail.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return string
     */
    function endsWithAny($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (Str::endsWith($haystack, $needle))
                return $needle;
        }

        return false;
    }
}

if (!function_exists('isInstanceOf')) {
    /**
     * Determine if a given object is an instance of second object
     *
     * @param $object
     * @param $ofThat
     *
     * @return bool
     */
    function isInstanceOf($object, $ofThat)
    {
        $noClass = function ($o) {
            return is_object($o) ? get_class($o) : $o;
        };

        try {
            $_ofThat = $noClass($ofThat);

            return ($object instanceof $ofThat) ||
                ($object instanceof $_ofThat) ||
                is_a($object, $ofThat) ||
                is_a($object, $_ofThat);

        } catch (Exception $exception) {

        }

        return false;
    }
}

if (!function_exists('isModel')) {
    /**
     * Determine if a given object is inherit Model class.
     *
     * @param object $object
     *
     * @return bool
     */
    function isModel($object)
    {
        try {
            return ($object instanceof Model) || is_a($object, Model::class);
        } catch (Exception $exception) {

        }

        return false;
    }
}

if (!function_exists('getModelKey')) {
    /**
     * Returns Model Key Only!
     *
     * @param $object
     *
     * @return mixed|object|int
     */
    function getModelKey($object)
    {
        if(isModel($object)) {
            $key = $object->getKeyName() ?: 'id';
            return $object->getKey() ?: $object->{$key} ?: (
                object_get($object, $key) ?:
                    array_get($object->toArray(), $key) ?: null
            );
        }

        return $object;
    }
}