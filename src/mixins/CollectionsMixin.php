<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\mixins;

use Illuminate\Support\Collection;

/**
 * Class CollectionsMixin
 *
 * @package App\mixins
 */
class CollectionsMixin
{
    /**
     * @return \Closure
     */
    public function mapKeysToCamelCase()
    {
        return function () {
            /* @var $this Collection */
            return $this->mapKeysWith('camel_case');
        };
    }

    /**
     * @return \Closure
     */
    public function mapKeysWith()
    {
        return function ($callable) {
            /* @var $this \Illuminate\Support\Collection */
            return $this->mapWithKeys(static function ($item, $key) use ($callable) {
                if (is_array($item)) {
                    $item = collect($item)
                        ->mapKeysWith($callable)
                        ->toArray();
                }
                return [$callable($key) => $item];
            });
        };
    }

}
