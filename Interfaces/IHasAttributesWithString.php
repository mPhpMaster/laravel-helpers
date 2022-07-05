<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Interfaces;

/**
 * Interface IHasAttributesWithString
 *
 * @package MPhpMaster\LaravelHelpers\Interfaces
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
