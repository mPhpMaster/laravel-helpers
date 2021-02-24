<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support;

use ArrayAccess;
use ArrayObject;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use mPhpMaster\Support\Interfaces\IMakable;
use Traversable;

/**
 * Class Optional
 *
 * @package mPhpMaster\Support
 */
class Optional implements ArrayAccess, Arrayable, Jsonable, \JsonSerializable, \Countable, \IteratorAggregate,
    IMakable
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The underlying object.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new optional instance.
     *
     * @param  mixed $value
     */
    public function __construct($value)
    {
        $this->setValue( toCollect($value)->all() );
    }

    /**
     * @param mixed[] ...$arguments
     *
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    /**
     * Dynamically access a property on the underlying object.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Dynamically check a property exists on the underlying object.
     *
     * @param  mixed $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        if (Str::contains($key, '.')) {
            return data_get($this->getValue(), $key, UNUSED) !== UNUSED;
        }

        return
            // collection check
            (is_collection($this->value) && $this->value->offsetExists($key)) ||
            // array check
            ((is_array($this->value) || $this->value instanceof ArrayObject) && isset($this->value[$key])) ||
            // object check
            (is_object($this->value) && isset($this->value->{$key}));
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return data_get($this->value, $key);
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed $key
     * @param  mixed $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_collection($this->value)) {
            $this->value->offsetSet($key, $value);
        } else {
            data_set($this->value, $key, $value);
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {

        if (is_collection($this->value)) {
            $this->value->offsetUnset($key);
        }
        if (is_object($this->value)) {
            unset($this->value->{$key});
        }
        if (is_array($this->value) || $this->value instanceof ArrayObject || $this->offsetExists($key)) {
            unset($this->value[$key]);
        }
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param  mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param  string|array $keys
     *
     * @return $this
     */
    public function forget($keys)
    {
        foreach ((array)$keys as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed $key
     * @param  mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return value($default);
    }

    /**
     * Put an item in the collection by key.
     *
     * @param  mixed $key
     * @param  mixed $value
     *
     * @return $this
     */
    public function put($key, $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Dynamically pass a method to the underlying object.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if ($this->offsetExists($method)) {
            $var = $this->offsetGet($method);
            if (is_callable($var)) {
                return $var(...$parameters);
            } else {
                return $var;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $obj = new ArrayObject($this->value);
        return $obj->getArrayCopy();
    }

    /**
     * @return \ArrayObject
     */
    public function getObjectCopy()
    {
        $obj = new ArrayObject($this->value);
        return $obj;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return $this->toJson();
    }


    /**
     * @param null $array
     *
     * @return object
     */
    public function toObject($array = null)
    {
        $array = $array ?: $this->getObjectCopy();
        $o = (object)[];
        foreach ($array as $key => $value) {
            $o->{$key} = is_array($value) ? (object)$value : $value;
        }
        return $o;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray($object = null)
    {
        $object = $object ?: $this->getArrayCopy();
        $o = [];
        foreach ($object as $key => $value) {
            $o[$key] = is_object($value) ? (array)$value : $value;
        }
        return $o;
    }

    /**
     * @return array|mixed
     */
    public function all()
    {
        return $this->value;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->value);
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed $items
     *
     * @return array
     */
    protected function getArrayableItems($items = null)
    {
        $items = $items ?: $this->value;
        if (is_array($items)) {
            return $items;
        } else if ($items instanceof self) {
            return $items->all();
        } else if ($items instanceof Arrayable) {
            return $items->toArray();
        } else if ($items instanceof Jsonable) {
            return json_decode($items->toJson(), true);
        } else if ($items instanceof \JsonSerializable) {
            return $items->jsonSerialize();
        } else if ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array)$items;
    }

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->toArray());
    }

    /**
     * Determine if the collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    /**
     * Dump the collection and end the script.
     *
     * @param  mixed ...$args
     *
     * @return void
     */
    public function d(...$args)
    {
        call_user_func_array([$this, 'dump'], $args);

        die(1);
    }

    /**
     * Dump the collection.
     *
     * @return $this
     */
    public function dump()
    {
        toCollect(func_get_args())
            ->push($this)
            ->each(function ($item) {
                dump($item);
            });

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
