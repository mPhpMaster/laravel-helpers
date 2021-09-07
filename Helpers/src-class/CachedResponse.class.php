<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Traits\Tappable;
use MPhpMaster\LaravelHelpers\Traits\TMacroable;

/**
 * Class CachedResponse
 */
class CachedResponse implements \IteratorAggregate, \Countable, Jsonable, Arrayable
{
    use Tappable,
        TMacroable;

    /**
     * @var false
     */
    protected bool $auto_save = false;
    /**
     * @var string
     */
    private $path = "cached_response/";
    /**
     * @var array
     */
    private $_data;
    /**
     * @var array
     */
    private $data;

    /**
     * CachedResponse constructor.
     */
    public function __construct($auto_save = false)
    {
        $this->path = str_finish(setting('dir.cached_response', $this->path), DIRECTORY_SEPARATOR);
        $this->initiate($auto_save);
    }

    /**
     * @param bool $auto_save
     *
     * @return $this
     */
    public function initiate(bool $auto_save)
    {
        $this->auto_save = $auto_save;
        $this->loadResponses();

        return $this;
    }

    public function loadResponses(bool $fresh = false)
    {
        static $loaded = false;

        $files = glob(fixPath(str_finish(base_path($this->path), DIRECTORY_SEPARATOR) . "*.json"));
        if ( !$fresh && $loaded ) {
            return $this;
        }

        $this->_data = $files;
        $this->data = [];

        foreach ($files as $file) {
            $name = head(explode('.json', basenameOf($file), 2));
            $data = file_get_contents($file);
            $this->data[ $name ] = valueFromJson($data);
            $this->data[ $name ] = $this->data[ $name ]['data'] ?? $this->data[ $name ];
        }

        $loaded = true;
        return $this;
    }

    public function saveResponses()
    {
        $getFilePath = function ($name) {
            return fixPath(str_finish(base_path($this->path), DIRECTORY_SEPARATOR) . "{$name}.json");
        };

        foreach ($this->data as $name => $data) {
            file_put_contents($filepath = $getFilePath($name), valueToJson(wrapWith($data, 'data')));
            $this->_data[ $name ] = $filepath;
        }

        return $this;
    }

    /**
     * @param string|\Closure|null $callback
     *
     * @return mixed|null
     */
    public function get($callback = null)
    {
        $data = $this->toArray();
        if ( is_string($callback) && isset($data[ $callback ]) ) {
            return $data[ $callback ];
        }

        if ( isClosure($callback) ) {
            return call_user_func($callback, $data);
        }

        if ( is_null($callback) ) {
            return $data;
        }

        return null;
    }

    /**
     * @param      $name
     * @param null $data
     *
     * @return $this
     */
    public function set($name, $data = null)
    {
        $this->data[ $name ] = $data;

        if ( $this->auto_save ) {
            $this->saveResponses();
        }

        return $this;
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        if ( isset($this->data[ $name ]) ) {
            unset($this->data[ $name ]);
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[ $name ]);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $method
     * @param array  $args
     */
    public function __call($method, $args)
    {
        if ( ($result = $this->handleMacroCall($method, $args)) && $result !== static::$MACRO_NOT_FOUND ) {
            return $result;
        }

        if ( !empty($args) && !isClosure(head($args)) ) {
            $this->set($method, count($args) === 1 ? head($args) : $args);
        }

        if ( isset($this->{$method}) ) {
            $data = $this->get($method);
            $fstArg = array_shift($args);
            if ( isClosure($fstArg) ) {
                return call_user_func($fstArg, [...$data, ...$args]);
            }

            array_unshift($args, $fstArg);
            return $data;
        } else {
            $this->set($method, null);
            return $this->__call($method, $args);
        }

        throw new LogicException([$method, $args], 200);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->toArray();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array)$this->data;
    }

    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * Returns the number of attributes.
     *
     * @return int The number of attributes
     */
    public function count()
    {
        return count($this->toArray());
    }

}
