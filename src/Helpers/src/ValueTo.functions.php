<?php
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

/**
 * return string
 */
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

        return $forceToArray ? $collect->toArray() : (is_array($collectAll = $collect->all()) ? $collectAll : $collect->toArray());
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
    function toDynamicObject(iterable $data): \mPhpMaster\Support\DynamicObject
    {
        return $data instanceof \DynamicObject ? $data : \DynamicObject::make($data);
    }
}
