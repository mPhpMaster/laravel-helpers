<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\mixins;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Execute a callable if the collection isn't empty, then return the collection.
 *
 * @mixin Collection
 *
 * @param callable callback
 *
 * @return \Illuminate\Support\Collection
 */
class PaginateInvoke
{
    /**
     * @return \Closure
     */
    public function __invoke()
    {
        /**
         * Paginate a standard Laravel Collection.
         *
         * @mixins Collection
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         *
         * @return LengthAwarePaginator
         */
        return function($perPage = null, array $only = ['*'], $pageName = 'page', $page = null, int $total = null): LengthAwarePaginator {
//        return function($perPage, $total = null, $page = null, $pageName = 'page'): LengthAwarePaginator {
            /** @var \Illuminate\Database\Eloquent\Model $self */
            $only = Collection::make($only)->filter(fn($i)=>$i&&$i!=='*')->toArray();
            $self = count($only) ? $this->only($only) : $this;
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $self->forPage($page, $perPage),
                $total ?: $self->count(),
                $perPage ?: 15,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        };
    }

    /**
     * @param       $items
     * @param int   $perPage
     * @param null  $page
     * @param null  $baseUrl
     * @param array $options
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($items, $perPage = 15, $page = null,
                             $baseUrl = null,
                             $options = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ?
            $items : Collection::make($items);

        $lap = new LengthAwarePaginator($items->forPage($page, $perPage),
                                        $items->count(),
                                        $perPage, $page, $options);

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;




//        $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
//        return new LengthAwarePaginator(
//            $this->forPage($page, $perPage),
//            $total ?: $this->count(),
//            $perPage,
//            $page,
//            [
//                'path' => LengthAwarePaginator::resolveCurrentPath(),
//                'pageName' => $pageName,
//            ]
//        );
    }
}
