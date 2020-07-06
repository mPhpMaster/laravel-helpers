<?php
/**
 * Copyright (c) $year. By: hlaCk (https://github.com/mPhpMaster)
 *
 */
namespace mPhpMaster\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \mPhpMaster\Support\ExtraMacros
 *
 * Class ExtraMacros
 */
class ExtraMacros extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'extra-macros';
    }
}
