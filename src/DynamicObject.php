<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 30/1/2020
 * Time: 3:31 PM
 */

namespace mPhpMaster\Support;


use Illuminate\Contracts\Support\Arrayable;

/**
 * Class DynamicObject
 *
 * @method array all()
 *
 * @package mPhpMaster\Support
 */
class DynamicObject extends \stdClass implements Arrayable
{

    /**
     * @param $key
     * @param $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($key, $params)
    {
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
    public static function make(iterable $data = [])
    {
        $obj = new self();
        foreach ($data as $key => $value) {
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
                if(is_numeric($key)) {
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
        $test = (array) $this;
        $test[] = 'TEST' . static::class;
        $newKey = (int) array_search('TEST' . static::class, $test);
        foreach ($data as $_data) {
            foreach ((array)$_data as $key => $value) {
                $key = intval(is_numeric($key) ? $newKey++ : $key);
                $this->$key = is_array($value) ? static::make($value) : $value;
            }
        }

        return $this;
    }
}
