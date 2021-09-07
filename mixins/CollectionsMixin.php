<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\mixins;

use Illuminate\Support\Collection;

/**
 * Class CollectionsMixin
 *
 * @property  array $items
 * @method \Illuminate\Pagination\LengthAwarePaginator paginate(?int $perPage = null, array $only = ['*'], string $pageName = 'page', ?int $page = null, ?null $total = null)
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
                if ( is_array($item) ) {
                    $item = collect($item)
                        ->mapKeysWith($callable)
                        ->toArray();
                }
                return [$callable($key) => $item];
            });
        };
    }

    /**
     * @return \Closure
     */
    public function mapKeys()
    {
        return function (callable $callback) {
            $result = [];

            foreach ($this->items as $key => $value) {
                $assoc = $callback($value, $key);

                if ( $assoc && is_array($assoc) ) {
                    foreach ($assoc as $mapKey => $mapValue) {
                        $result[ $mapKey ] = $mapValue;
                    }
                }
            }

            return new static($result);
        };
    }

    /**
     * @return \Closure
     */
    public function isAssoc()
    {
        return function () {
            /* @var $this Collection */
            return \Illuminate\Support\Arr::isAssoc($this->toBase()->all());
        };
    }
}
