<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Facades\Route;

/**
 * return appLocale
 */
if (!function_exists('currentLocale')) {
    /**
     * return appLocale
     *
     * @return string
     */
    function currentLocale($full = false): string
    {
        if ($full)
            return (string)app()->getLocale();

        $locale = current(explode("-", app()->getLocale()));
        return $locale ?: "";
    }
}

if (!function_exists('currentActionName')) {
    /**
     * @param null $action
     *
     * @return null
     */
    function currentActionName($action = null)
    {
        $action = $action ?:
            Route::current()->getActionName() ?:
                currentRoute()->getActionMethod() ?:
                    Route::currentRouteAction() ?:
                        Route::current()->getName() ?:
                            null;

        $methodName = $action ? getMethodName($action) : null;

        return $methodName ?: null;
    }
}

if (!function_exists('currentModel')) {
    /**
     * Returns current model form route
     *
     * @param null $default
     * @return null
     */
    function currentModel($default = null)
    {
        return array_first(currentRoute()->parameters()) ?: $default;
    }
}

if (!function_exists('currentUrl')) {
    /**
     * Returns current url.
     *
     * @param string|null $key return as array with key $key and value as url
     * @param bool $encode use urlencode
     *
     * @return string|array
     */
    function currentUrl(?string $key = null,bool $encode = true)
    {
        $url = request()->url();
        $url = iif($encode, urlencode($url), $url);

        return is_null($key) ? $url : [ $key => $url ];
    }
}
