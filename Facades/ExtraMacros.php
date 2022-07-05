<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */
namespace MPhpMaster\LaravelHelpers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \MPhpMaster\LaravelHelpers\ExtraMacros
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
