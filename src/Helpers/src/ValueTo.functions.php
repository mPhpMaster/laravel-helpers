<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

/**
 * return string
 */

if ( !function_exists('valueToDate') ) {
    /**
     * Returns value as date format
     *
     * @param $value
     *
     * @return string|null
     */
    function valueToDate($value)
    {
        return $value ? carbon()->parse($value)->toDateString() : null;
    }
}

if ( !function_exists('valueToDateTime') ) {
    /**
     * Returns value as date and time format
     *
     * @param $value
     *
     * @return string|null
     */
    function valueToDateTime($value)
    {
        return $value ? carbon()->parse($value)->toDateTimeString() : null;
    }
}

/**
 * return array
 */
if ( !function_exists('valueToArray') ) {
    /**
     * Returns value as Array
     *
     * @param      $value
     * @param bool $forceToArray
     *
     * @return null|array
     */
    function valueToArray($value, bool $forceToArray = false)
    {
        if ( $value instanceof Traversable ) {
            return iterator_to_array($value);
        }
        $collect = toCollect($value);

        return !$forceToArray && is_array($collectAll = $collect->all()) ? $collectAll : $collect->toArray();
    }
}

/**
 * return array
 */
if ( !function_exists('valueToUnDotArray') ) {
    /**
     * Returns value as Array. (Array undot)
     *
     * @param $value
     *
     * @return null|array
     */
    function valueToUnDotArray($value)
    {
        $array = [];

        collect($value)->mapWithKeys(function ($value, $key) use (&$array) {
            return array_set($array, $key, $value);
        });

        return $array;
    }
}

if ( !function_exists('valueToDotArray') ) {
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param iterable $array
     * @param string   $prepend
     *
     * @return array
     */
    function valueToDotArray($array, $prepend = '')
    {
        $results = [];

        foreach ((array)$array as $key => $value) {
            if ( !empty($value) && is_array($value) ) {
                $results[] = valueToDotArray($value, $prepend . $key . '.');
            } else {
                $results[] = [$prepend . $key => $value];
            }
        }
        $results = count($results) === 1 ? head($results) : array_merge(...$results);

        return $results;
    }
}

/**
 * return object
 */
if ( !function_exists('valueToObject') ) {
    /**
     * Cast value as Object
     *
     * @param $value
     *
     * @return object
     */
    function valueToObject($value)
    {
        return (object)$value;
    }
}

if ( !function_exists('valueFromJson') ) {
    /**
     * @param string|null $_data
     * @param null|mixed  $default
     *
     * @return array|mixed
     */
    function valueFromJson(?string $_data, $default = null)
    {
        try {
            $data = json_decode($_data, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $exception) {
            $data = value($default ?? false);
        }

        return $data;
    }
}

if ( !function_exists('valueToJson') ) {
    /**
     * @param string|array|null $_data
     * @param null|mixed        $default
     * @param int               $options
     *
     * @return string|mixed
     */
    function valueToJson($_data = null, $default = null, $options = 0)
    {
        $_data = is_string($_data) ? valueFromJson($_data, $_data) : $_data;
        try {
            $data = json_encode($_data, $options);
        } catch (\Exception $exception) {
            $data = value($default ?? false);
        }

        return $data;
    }
}

if ( !function_exists('getValue') ) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    function getValue($value, ...$arguments)
    {
        return isClosure($value) || isCallable($value) ? $value(...$arguments) : $value;
    }
}

/**
 * return object
 */
if ( !function_exists('arrayToObject') ) {
    /**
     * Returns array as Object
     *
     * @param $value
     *
     * @return object
     */
    function arrayToObject($value)
    {
        if ( is_object($value) || is_array($value) ) {

            //            $_value->by = "hlack";
            return json_decode(json_encode($value));
        }

        $object = (object)[];
        foreach ((array)$value as $key => $item) {
            if ( is_array($item) ) {
                $object->$key = arrayToObject($item);
            } else {
                $object->$key = $item;
            }
        }

        return $object;
    }
}

if ( !function_exists('arrayToStdClass') ) {
    /**
     * Returns value as Object
     *
     * @param $value
     *
     * @return object
     */
    function arrayToStdClass(array $value)
    {
        $stdClass = new \stdClass;
        $item = null;
        foreach ($value as $key => &$item) {
            $stdClass->$key = is_array($item) ? arrayToStdClass($item) : $item;
        }
        unset($item);

        return $stdClass;
    }
}

if ( !function_exists('toVar') ) {
    /**
     * Returns value as boolean
     *
     * @param $var
     *
     * @return bool
     */
    function toVar($value = null, \Closure $callable = null): Closure
    {
        if ( $callable && ($callable instanceof \Closure) ) {
            return function () use (&$callable, &$value) {
                /** @var $callable \Closure */
                return $callable->call(new class ($value) {
                    /**
                     * @var mixed
                     */
                    public $var = null;

                    /**
                     *  constructor.
                     *
                     * @param mixed $var
                     */
                    public function __construct(&$var = null)
                    {
                        $this->var = &$var;
                    }

                    /**
                     * @return string
                     */
                    public function __toString()
                    {
                        return (string)$this->var;
                    }
                }, ...func_get_args());
            };
        }

        return function () use (&$value) {
            return $value;
        };
    }
}

if ( !function_exists('toDynamicObject') ) {
    /**
     * @param iterable $data
     *
     * @return \mPhpMaster\Support\DynamicObject
     */
    function toDynamicObject($data, array $except = []): \mPhpMaster\Support\DynamicObject
    {
        return $data instanceof \DynamicObject ? $data : \DynamicObject::make($data, $except);
    }
}

if ( !function_exists('toVarObject') ) {
    /**
     * @param mixed      $value
     * @param mixed|null $key
     *
     * @return \VarObject
     */
    function toVarObject($value, $key = null): VarObject
    {
        return new VarObject($value, $key);
    }
}

if ( !function_exists('trimLower') ) {
    /**
     * @param string $string
     *
     * @return string
     */
    function trimLower(?string $string)
    {
        return strtolower(trim($string));
    }
}

if ( !function_exists('trimUpper') ) {
    /**
     * @param string $string
     *
     * @return string
     */
    function trimUpper(?string $string)
    {
        return strtoupper(trim($string));
    }
}

if ( !function_exists('withKey') ) {
    /**
     * If the given data is not an array, wrap it in one.
     * If the given data is array and doesn't has $key ? add $key with $key_default_value.
     *
     * @param array|mixed $value
     * @param string      $key
     * @param mixed      $key_default_value
     *
     * @return array|array[]
     */
    function withKey($value, string $key, $key_default_value = []): array
    {
        $value = is_array($value) ? $value : [$value];
        if ( isset($value[ $key ]) ) {
            return $value;
        }
        $value[ $key ] = $key_default_value;
        return $value;
    }
}

if ( !function_exists('wrapWith') ) {
    /**
     * If the given value is not an array, wrap it in one. and assign it to the given key.
     *
     * @param array|mixed $value
     * @param string      $key
     *
     * @return array|array[]
     */
    function wrapWith($value, string $key = null): array
    {
        if ( is_array($value) ) {
            if ( is_null($key) ) {
                return array_wrap($value);
            }
            if ( isset($value[ $key ]) ) {
                return $value;
            }
        }

        return !is_null($key) ? array_add([], $key, $value) : array_wrap($value);
    }
}

if ( !function_exists('wrapWithData') ) {
    /**
     * Wrap the given value with 'data' key or return it if already wrapped.
     *
     * @param array|mixed $value
     *
     * @return array|array[]
     */
    function wrapWithData($value): array
    {
        $key = 'data';
        if ( is_array($value) ) {
            if ( isset($value[ $key ]) ) {
                return $value;
            }
        }

        return array_add([], $key, $value);
    }
}

if ( !function_exists('wrapWithData') ) {
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array  $array
     * @param string $prepend
     *
     * @return array
     */
    function wrapWithData($array, $prepend = ''): array
    {
        $key = 'data';
        if ( is_array($value) ) {
            if ( isset($value[ $key ]) ) {
                return $value;
            }
        }

        return array_add([], $key, $value);
    }
}

if ( !defined('NO_CHANGE') ) {
    define('NO_CHANGE', "no-change");
}

if ( !function_exists('unwrapWith') ) {
    /**+
     * like data_get
     *
     * @param array|mixed          $data
     * @param string|null          $key
     * @param mixed|null|NO_CHANGE $default
     *
     * @return array|null|mixed
     */
    function unwrapWith($data, string $key = null, $default = null)
    {
        $default = $default === NO_CHANGE ? $data : $default;
        $data = ($_data = getArrayableItems($data)) == [$data] ? $data : $_data;
        if ( is_array($data) ) {
            if ( is_null($key) ) {
                return isAssocArray($data) ? $default : head($data);
            }

            return data_get($data, $key, $default);
        }

        return $default;
    }
}
