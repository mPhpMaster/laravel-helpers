<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
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
