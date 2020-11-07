<?php
/**
 * Copyright آ© 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use App\Helpers\Interfaces\IInvocable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if ( !function_exists('array_keys_exists') ) {
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
if ( !function_exists('isArrayable') ) {
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
if ( !function_exists('isAssocArray') ) {
    /**
     * @param array $array
     *
     * @return bool
     */
    function isAssocArray(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}

/**
 * return bool
 */
if ( !function_exists('isClosure') ) {
    /**
     * Check if the given var is Closure.
     *
     * @param mixed|null $closure
     *
     * @return bool
     */
    function isClosure($closure): bool
    {
        return $closure instanceof Closure;
    }
}

/**
 * return bool
 */
if ( !function_exists('isCallableDeep') ) {
    /**
     * Check if the given var is Closure or|and callable or|and string or|and Array or|and object.
     *
     * @param mixed $closure
     * @param bool  $mustBeCallable
     * @param bool  $mustBeClosure
     * @param bool  $mustBeString
     * @param bool  $mustBeArray
     * @param bool  $mustBeObject
     *
     * @return bool
     */
    function isCallableDeep($closure,
                            bool $mustBeCallable = false,
                            bool $mustBeClosure = false,
                            bool $mustBeString = false,
                            bool $mustBeArray = false,
                            bool $mustBeObject = false): bool
    {
        $_result = [
            'callable' => is_callable($closure),
            'closure' => isClosure($closure),
            'string' => is_string($closure),
            'array' => is_array($closure),
            'object' => is_object($closure),
        ];

        if(
            ($mustBeCallable === true && $_result['callable'] === false) ||
            ($mustBeClosure === true && $_result['closure'] === false) ||
            ($mustBeString === true && $_result['string'] === false) ||
            ($mustBeArray === true && $_result['array'] === false) ||
            ($mustBeObject === true && $_result['object'] === false)
        ) {
            return false;
        }
        if(
            ($mustBeCallable === false && $_result['callable'] === true) ||
            ($mustBeClosure === false && $_result['closure'] === true) ||
            ($mustBeString === false && $_result['string'] === true) ||
            ($mustBeArray === false && $_result['array'] === true) ||
            ($mustBeObject === false && $_result['object'] === true)
        ) {
            return true;
        }

        return count(filterEach($_result, true)) > 0;
//        $result = $mustBeCallable ? is_callable($closure) : $result;
//        $result = $mustBeClosure ? isClosure($closure) : $result;
//        $result = $mustBeString ? is_string($closure) : $result;
//        $result = $mustBeArray ? is_array($closure) : $result;
//        $result = $mustBeObject ? is_object($closure) : $result;
//
//        return $result;
    }
}

/**
 * return bool
 */
if ( !function_exists('isCallableDeep') ) {
    /**
     * Check if the given var is Closure or|and callable or|and string or|and Array or|and object.
     *
     * @param mixed $closure
     * @param bool  $mustBeCallable
     * @param bool  $mustBeClosure
     * @param bool  $mustBeString
     * @param bool  $mustBeArray
     * @param bool  $mustBeObject
     *
     * @return bool
     */
    function isCallableDeep($closure,
                            bool $mustBeCallable = false,
                            bool $mustBeClosure = false,
                            bool $mustBeString = false,
                            bool $mustBeArray = false,
                            bool $mustBeObject = false): bool
    {
        $_result = [
            'callable' => is_callable($closure),
            'closure' => isClosure($closure),
            'string' => is_string($closure),
            'array' => is_array($closure),
            'object' => is_object($closure),
        ];

        if(
            ($mustBeCallable === true && $_result['callable'] === false) ||
            ($mustBeClosure === true && $_result['closure'] === false) ||
            ($mustBeString === true && $_result['string'] === false) ||
            ($mustBeArray === true && $_result['array'] === false) ||
            ($mustBeObject === true && $_result['object'] === false)
        ) {
            return false;
        }
        if(
            ($mustBeCallable === false && $_result['callable'] === true) ||
            ($mustBeClosure === false && $_result['closure'] === true) ||
            ($mustBeString === false && $_result['string'] === true) ||
            ($mustBeArray === false && $_result['array'] === true) ||
            ($mustBeObject === false && $_result['object'] === true)
        ) {
            return true;
        }

        return count(filterEach($_result, true)) > 0;
//        $result = $mustBeCallable ? is_callable($closure) : $result;
//        $result = $mustBeClosure ? isClosure($closure) : $result;
//        $result = $mustBeString ? is_string($closure) : $result;
//        $result = $mustBeArray ? is_array($closure) : $result;
//        $result = $mustBeObject ? is_object($closure) : $result;
//
//        return $result;
    }
}

/**
 * return bool
 */
if ( !function_exists('isCallable') ) {
    /**
     * Check if the given var is callable && not string.
     *
     * @param mixed|null $callable
     *
     * @return bool
     */
    function isCallable($callable): bool
    {
//        return is_callable($callable)/* && !is_string($callable)*/ && !($callable instanceof Closure);
        return is_callable($callable) && !is_string($callable)/* && !($callable instanceof Closure)*/;
    }
}

/**
 * return bool
 */
if ( !function_exists('isArrayableOrArray') ) {
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
if ( !function_exists('isAllable') ) {
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
if ( !function_exists('isInvocable') ) {
    /**
     * @param mixed $object
     *
     * @return bool
     */
    function isInvocable($object): bool
    {
        return
            method_exists($object, '__invoke') ||
            is_callable($object) ||
            $object instanceof IInvocable;
    }
}

/**
 * return bool
 */
if ( !function_exists('isPaginator') ) {
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
if ( !function_exists('isPaginated') ) {
    /**
     * Check if the given var is paginate result.
     *
     * @param $value
     *
     * @return null
     */
    function isPaginated($value)
    {
        if ( isPaginator($value) || method_exists($value, 'getCollection') ) {
            return true;
        }

        if ( is_array($value) ) {
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
if ( !function_exists('isConsole') ) {
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
if ( !function_exists('isBuilder') ) {
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
if ( !function_exists('isLoggedIn') ) {
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
if ( !function_exists('isGuest') ) {
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

if ( !function_exists('endsWithAny') ) {
    /**
     * Determine if a given string ends with a given substrings then return substring or False when fail.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return string
     */
    function endsWithAny($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ( Str::endsWith($haystack, $needle) )
                return $needle;
        }

        return false;
    }
}

/**
 * return bool
 */
if ( !function_exists('isInstanceOf') ) {
    /**
     * ### Check if the given object\objects is an instance of second object.
     *
     * @param array|object|mixed  $object
     * @param string|object|mixed $ofThat
     * @param callable|null       $then
     *
     * @return bool
     */
    function isInstanceOf($object, $ofThat, ?callable $then = null): bool
    {
        $applyThen = function (&$_object, &$_ofThat) use (&$then) {
            return isClosure($then) ? $then($_object, $_ofThat) : $then;
        };

        $instance_found = 0;
        try {
            $object = valueToArray($object);
            foreach ($object as $_object) {
                foreach ((array)$ofThat as $_ofThat) {
                    if ( ($_object instanceof $_ofThat) || is_a($_object, $_ofThat) ) {
                        $instance_found++;
                        $applyThen($_object, $_ofThat);
                    }

                    $__ofThat = $_ofThat;
                    if ( ($_ofThat = getClass($__ofThat)) !== $__ofThat &&
                        (
                            ($_object instanceof $_ofThat) || is_a($_object, $_ofThat)
                        )
                    ) {
                        $instance_found++;
                        $applyThen($_object, $_ofThat);
                    }
                }
            }

        } catch (Exception $exception) {

        }

        return is_array($object) && $instance_found === count($object);
    }
}

if ( !function_exists('isInstanceOfAnyOf') ) {
    /**
     * Determine if any of the given object\objects is an instance of any of second object\objects
     *
     * @param array|object|mixed $objects
     * @param array|object|mixed $ofThese
     *
     * @return bool
     */
    function isInstanceOfAnyOf($objects, $ofThese, ?callable $then = null)
    {
        try {
            foreach ((array)$objects as $_object) {
                foreach ((array)$ofThese as $_ofThat) {
                    if ( isInstanceOf($_object, $_ofThat, $then) ) {
                        return true;
                    }
                }
            }

        } catch (Exception $exception) {

        }

        return false;
    }
}

if ( !function_exists('isModel') ) {
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

if ( !function_exists('isRelation') ) {
    /**
     * Determine if a given object is inherit \Illuminate\Database\Eloquent\Relations\Relation class.
     *
     * @param object $object
     *
     * @return bool
     */
    function isRelation($object)
    {
        try {
            return (isInstanceOf($object, \Illuminate\Database\Eloquent\Relations\Relation::class)) || is_a($object, \Illuminate\Database\Eloquent\Relations\Relation::class);
        } catch (Exception $exception) {

        }

        return false;
    }
}

if ( !function_exists('isDynamicObject') ) {
    /**
     * @param DynamicObject|mixed $data
     *
     * @return bool
     */
    function isDynamicObject($data): bool
    {
        return $data instanceof DynamicObject;
    }
}

if ( !function_exists('hasArabicChars') ) {
    /**
     * @param $string |mixed $string
     *
     * @return bool
     */
    function hasArabicChars($string): bool
    {
        return preg_match("/[\x{0600}-\x{06FF}\x]{1,32}/u", $string);
    }
}
