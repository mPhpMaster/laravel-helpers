<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Traits;

/**
 * Trait ForwardsAllCallsToInstance
 *
 * @package mPhpMaster\Support\Traits
 */
trait TForwardsAllCallsToInstance
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
        return (new static)->forwardAllCallsTo(...func_get_args());
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
        return $this->forwardAllCallsTo(...func_get_args());
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    abstract function forwardAllCallsTo($name, $arguments);
}
