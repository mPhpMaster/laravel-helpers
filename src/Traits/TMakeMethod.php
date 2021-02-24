<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Traits;

/**
 * Trait TMakeMethod
 *
 * <i>IMakable Interface {@link \mPhpMaster\Support\Interfaces\IMakable (IMakable Interface)}</>
 *
 * @mixin \mPhpMaster\Support\Interfaces\IMakable
 * @depends \mPhpMaster\Support\Interfaces\IMakable
 *
 * @package mPhpMaster\Support\Traits
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
