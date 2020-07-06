<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 30/1/2020
 * Time: 3:31 PM
 */

namespace mPhpMaster\Support;


/**
 * Class DynamicObject
 *
 * @package mPhpMaster\Support
 */
class DynamicObject extends \stdClass {

    /**
     * @param $key
     * @param $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($key, $params)
    {
        if ( ! isset($this->{$key})) {
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
        foreach ($data as $key=>$value) {
            $obj->$key = $value;
        }

        return $obj;
    }
}
