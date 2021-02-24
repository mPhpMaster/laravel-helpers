<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Interfaces;

/**
 * Interface IInvocable
 *
 * @package mPhpMaster\Support\Interfaces
 */
interface IInvocable
{
    /**
     * @param mixed[] ...$attributes
     *
     * @return mixed
     */
    public function __invoke(...$attributes);
}
