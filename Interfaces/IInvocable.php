<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Interfaces;

/**
 * Interface IInvocable
 *
 * @package MPhpMaster\LaravelHelpers\Interfaces
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
