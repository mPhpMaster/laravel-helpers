<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
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
