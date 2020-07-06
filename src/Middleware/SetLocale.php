<?php

namespace mPhpMaster\Support\Middleware;

use Closure;

/**
 * Class SetLocale
 *
 * @package mPhpMaster\Support\Middleware
 */
class SetLocale
{
    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( request('change_language') ) {
            session()->put('language', request('change_language'));
            $language = request('change_language');
        } elseif ( session('language') ) {
            $language = session('language');
        } elseif ( config('panel.primary_language') ) {
            $language = config('panel.primary_language');
        }

        if ( isset($language) ) {
            app()->setLocale($language);
        }

        return $next($request);
    }
}
