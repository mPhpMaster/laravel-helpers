<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 26/8/2019
 * Time: 10:56 AM
 */

namespace mPhpMaster\Support\Traits;

/**
 * Trait TForwardsGetToInstance
 *
 * @package mPhpMaster\Support\Traits
 */
trait TForwardsGetToInstance
{
    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->forwardGetTo(...func_get_args());
    }

    /**
     * @return mixed
     */
    abstract function forwardGetTo($name);
}
