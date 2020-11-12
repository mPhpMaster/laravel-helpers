<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
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
