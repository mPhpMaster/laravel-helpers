<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Tappable;
use mPhpMaster\Support\Traits\TMacroable;

/**
 * Class DynamicObject
 *
 * @method array all()
 *
 * @package mPhpMaster\Support
 */
class DynamicObject extends \stdClass implements Arrayable, \ArrayAccess
{
    use Tappable,
        TMacroable;

    private const DELETED = "@@@deleted";

    /**
     * @param $key
     * @param $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($key, $params)
    {
        if ( ($result = $this->handleMacroCall($key, $params)) && $result !== static::$MACRO_NOT_FOUND ) {
            return $result;
        }

        if ( !isset($this->{$key}) ) {
            if ( $key === 'all' ) {
                return $this->toArray();
            }
            throw new \Exception("Call to undefined method " . __CLASS__ . "::" . $key . "()");
        }

        return $this->{$key}->__invoke(... $params);
    }

    /**
     * @param array|iterable $data
     *
     * @return \mPhpMaster\Support\DynamicObject
     */
    public static function make($data = [], array $except = [])
    {
        $obj = new self();
        foreach ($data as $key => $value) {
            if ( in_array($key, $except) ) {
                continue;
            }
            if (
                is_object($value) && (
                    isset($value->toArray) || method_exists($value, 'toArray')
                )
            ) {
                $value = $value->toArray(request());
            }

            $obj->$key = is_array($value) ? static::make($value) : $value;
        }

        return $obj;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = (array)$this;
        foreach ($data as $key => $value) {
            if ( $value instanceof static ) {
                $data[ $key ] = $value->toArray();
            } else if (
                is_object($data) && (
                    isset($data->toArray) || method_exists($data, 'toArray')
                )
            ) {
                $data[ $key ] = $value->toArray(request());
            }
        }

        return $data;
    }

    /**
     * merge data with the given data.
     *
     * @param array ...$data
     *
     * @return static
     */
    public function merge(...$data)
    {
        foreach ($data as $_data) {
            foreach ((array)$_data as $key => $value) {
                $value = is_array($value) ? static::make($value) : $value;
                if ( is_numeric($key) ) {
                    $this->add($value);
                } else {
                    $this->$key = $value;
                }
            }
        }

        return $this;
    }

    /**
     * add data to new key.
     *
     * @param array ...$data
     *
     * @return static
     */
    public function add(...$data)
    {
        $test = (array)$this;
        $test[] = 'TEST' . static::class;
        $newKey = (int)array_search('TEST' . static::class, $test);
        foreach ($data as $_data) {
            foreach ((array)$_data as $key => $value) {
                $key = intval(is_numeric($key) ? $newKey++ : $key);
                $this->$key = is_array($value) ? static::make($value) : $value;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->toArray());
    }

    /**
     * is triggered by calling isset() or empty() on inaccessible members.
     *
     * @param $name string
     *
     * @return bool
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __isset($name)
    {
        $data = $this->toArray();
        return isset($data[ $name ]);
    }

    /**
     * is invoked when unset() is used on inaccessible members.
     *
     * @param $name string
     *
     * @return void
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __unset($name)
    {
        if ( isset($this->{$name}) ) {
            unset($this->$name);
        }
    }

    /**
     * @param array $except
     *
     * @return \mPhpMaster\Support\DynamicObject
     */
    public function except(array $except = [])
    {
        return static::make($this->toArray(), $except);
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
}
