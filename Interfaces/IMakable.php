<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Interfaces;

use MPhpMaster\LaravelHelpers\Traits\TMakeMethod;

/**
 * Interface IMackable
 *
 * @uses \MPhpMaster\LaravelHelpers\Traits\TMakeMethod
 */
interface IMakable
{

    /**
     * @return static
     */
    static function make();
}
