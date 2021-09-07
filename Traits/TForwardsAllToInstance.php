<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Traits;

/**
 * Trait TForwardsAllToInstance
 *
 * @package MPhpMaster\LaravelHelpers\Traits
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
