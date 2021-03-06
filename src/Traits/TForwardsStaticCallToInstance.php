<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
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
