<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 26/8/2019
 * Time: 10:56 AM
 */

namespace mPhpMaster\Support\Traits;

/**
 * Trait TForwardsAllToInstance
 *
 * @package mPhpMaster\Support\Traits
 */
trait TForwardsAllToInstance
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
        return (new static)->forwardAllTo(...func_get_args());
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $args = func_get_args();
        $args = count($args) < 2 ? $args+[null] : $args;
        return $this->forwardAllTo(...$args);
    }

    /**
     * @param $name
     *
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->forwardAllTo(...func_get_args());
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    abstract function forwardAllCallsTo($name,array $arguments = []);

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    abstract function forwardAllGetsTo($name,array $arguments = []);
}
