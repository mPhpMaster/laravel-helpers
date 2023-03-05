<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\mixins;

use Illuminate\Support\Collection;

/**
 * Execute a callable if the collection isn't empty, then return the collection.
 *
 * @param callable callback
 *
 * @return \Illuminate\Support\Collection
 */
class GlobInvoke
{
    /**
     * @return \Closure
     */
    public function __invoke()
    {
        return static function (string $pattern, int $flags = 0): Collection {
            return Collection::make(glob($pattern, $flags));
        };
    }
}
