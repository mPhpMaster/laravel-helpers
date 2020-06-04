<?php
/**
 * return string
 */
if (!function_exists('valueToDate')) {
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
if (!function_exists('valueToDateTime')) {
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
if (!function_exists('valueToArray')) {
    /**
     * Returns value as Array
     *
     * @param $value
     *
     * @param bool $forceToArray
     * @return null|array
     */
    function valueToArray($value, bool $forceToArray = false)
    {
        if ($value instanceof Traversable) {
            return iterator_to_array( $value );
        }
        $collect = toCollect($value);

        return $forceToArray ? $collect->toArray() : (is_array($collectAll = $collect->all()) ? $collectAll : $collect->toArray());
    }
}

/**
 * return array
 */
if (!function_exists('valueToDotArray')) {
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

        collect($value)->mapWithKeys(function ($value, $key) use(&$array) {
            return array_set($array, $key, $value);
        });

        return $array;
    }
}

/**
 * return object
 */
if (!function_exists('valueToObject')) {
    /**
     * Returns value as Object
     *
     * @param $value
     *
     * @return null|object
     */
    function valueToObject($value)
    {
        return (object)$value;
    }
}
