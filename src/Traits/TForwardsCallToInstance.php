<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 26/8/2019
 * Time: 10:56 AM
 */

namespace mPhpMaster\Support\Traits;

/**
 * Trait TForwardsCallToInstance
 *
 * @package mPhpMaster\Support\Traits
 */
trait TForwardsCallToInstance
{
    /**
     * @param $name
     *
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->forwardCallTo(...func_get_args());
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    abstract function forwardCallTo($name, $arguments);
}
