<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 26/8/2019
 * Time: 10:56 AM
 */

namespace mPhpMaster\Support\Traits;

/**
 * Trait TForwardsStaticCallToNewInstance
 *
 * @package mPhpMaster\Support\Traits
 */
trait TForwardsStaticCallToNewInstance
{

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return (new static)->{$method}(...$args);
    }

}
