<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Traits;

/**
 * Trait TMakeMethod
 *
 * <i>IMakable Interface {@link \MPhpMaster\LaravelHelpers\Interfaces\IMakable (IMakable Interface)}</>
 *
 * @mixin \MPhpMaster\LaravelHelpers\Interfaces\IMakable
 * @depends \MPhpMaster\LaravelHelpers\Interfaces\IMakable
 *
 * @package MPhpMaster\LaravelHelpers\Traits
 */
trait TMakeMethod
{

    /**
     * TMakeMethod constructor.
     *
     * @param array|array[] $attributes
     */
    abstract function __construct(...$attributes);

    /**
     * @param array|array[] $attributes
     *
     * @return static
     */
    public static function make(...$attributes)
    {
        return newInstance(static::class, func_get_args());
    }
}
