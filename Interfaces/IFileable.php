<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Interfaces;

/**
 * Interface IFileable
 *
 * @package MPhpMaster\LaravelHelpers\Interfaces
 */
interface IFileable
{
    public function toFilename(): string;
}
