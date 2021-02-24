<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

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
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( request('change_language') ) {
            $request->session()->put('language', request('change_language'));
            $request->session()->save();

            $language = request('change_language');
        } elseif ( $request->session()->has('language') ) {
            $language = $request->session()->get('language');
        } elseif ( config('app.locale') ) {
            $language = config('app.locale');
        }

        if ( isset($language) ) {
            app()->setLocale($language);
            if ( request('change_language') ) {
                return redirect()->back();
            }
        }

        return $next($request);
    }
}
