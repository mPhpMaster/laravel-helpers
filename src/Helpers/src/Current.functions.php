<?php
/*
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

use Illuminate\Support\Facades\Route;

/**
 * return appLocale
 */
if (!function_exists('currentLocale')) {
    /**
     * return appLocale
     *
     * @param bool $full
     *
     * @return string
     */
    function currentLocale($full = false): string
    {
        if ($full)
            return (string)app()->getLocale();

        $locale = str_replace('_', '-', app()->getLocale());
        $locale = current(explode("-", $locale));
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

if (!function_exists('routeParameter')) {
    /**
     * @param array $default
     *
     * @return array|mixed|null
     */
    function routeParameter($key = null, $default = null)
    {
        $parameters = currentRoute()->parameters;

        if(!$parameters) {
            return $default;
        }

        return  is_null($key) ? $parameters : array_get($parameters, $key, $default);
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
