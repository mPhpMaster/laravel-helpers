<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Traits;

/**
 * Trait TForwardsGetToInstance
 *
 * @package MPhpMaster\LaravelHelpers\Traits
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
