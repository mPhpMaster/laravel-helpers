<?php
/*
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
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
     * @return null
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
     * @return null
     */
    function valueToDateTime($value)
    {
        return $value ? carbon()->parse($value)->toDateTimeString() : null;
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
    function wrapWith($value, string $key): array
    {
        if ( is_array($value) ) {
            if ( isset($value[ $key ]) ) {
                return $value;
            }
        }

        return [$key => $value];
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
     *
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
if ( !function_exists('valueToDotArray') ) {
    /**
     * Returns value as Array
     *
     * @param $value
     *
     * @return null|array
     */
    function valueToDotArray($value)
    {
        $array = [];

        collect($value)->mapWithKeys(function ($value, $key) use (&$array) {
            return array_set($array, $key, $value);
        });

        return $array;
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
            $data = json_decode($_data, true);
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
     *
     * @return string|mixed
     */
    function valueToJson($_data = null, $default = null)
    {
        $_data = is_string($_data) ? valueFromJson($_data, $_data) : $_data;
        try {
            $data = json_encode($_data);
        } catch (\Exception $exception) {
            $data = value($default ?? false);
        }

        return $data;
    }
}

if ( !function_exists('trimLower') ) {
    /**
     * @param string $string
     *
     * @return string
     */
    function trimLower(string $string)
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
    function trimUpper(string $string)
    {
        return strtoupper(trim($string));
    }
}
