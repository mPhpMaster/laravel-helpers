<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Interfaces;

use mPhpMaster\Support\Traits\TMakeMethod;

/**
 * Interface IMackable
 *
 * @uses \mPhpMaster\Support\Traits\TMakeMethod
 */
interface IMakable
{

    /**
     * @return static
     */
    static function make();
}
