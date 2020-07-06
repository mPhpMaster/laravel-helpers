<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 26/8/2019
 * Time: 10:56 AM
 */

namespace mPhpMaster\Support\Traits;

/**
 * Trait TForwardsStaticCallToInstance
 *
 * @package mPhpMaster\Support\Traits
 */
trait TForwardsStaticCallToInstance
{
    /**
     * @param $name
     *
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return (new static)->forwardStaticCallTo(...func_get_args());
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    abstract function forwardStaticCallTo($name, $arguments);
}
