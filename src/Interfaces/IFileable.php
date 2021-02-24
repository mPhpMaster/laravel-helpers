<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\Interfaces;

/**
 * Interface IFileable
 *
 * @package mPhpMaster\Support\Interfaces
 */
interface IFileable
{
    public function toFilename(): string;
}
