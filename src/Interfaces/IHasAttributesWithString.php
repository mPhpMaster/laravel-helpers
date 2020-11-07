<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Interfaces;

/**
 * Interface IHasAttributesWithString
 *
 * @package mPhpMaster\Support\Interfaces
 */
interface IHasAttributesWithString
{
    /**
     * Returns all allowed strings name & value.
     *
     * @return array [ name => string ]
     */
    public static function getAllowedStrings(): array;

    /**
     * Returns method that apply the suffixing.
     *
     * @return \Closure
     */
    public static function getDefaultStringSuffixer(): \Closure;
}
