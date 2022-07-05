<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Traits;

/**
 * Trait TForwardsStaticCallToNewInstance
 *
 * @package MPhpMaster\LaravelHelpers\Traits
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
