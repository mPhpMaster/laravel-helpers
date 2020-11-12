<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Traits;

/**
 * Trait TForwardsStaticCallToNewInstance
 *
 * @package mPhpMaster\Support\Traits
 */
trait TForwardsStaticCallToNewInstance
{

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return (new static)->{$method}(...$args);
    }

}
