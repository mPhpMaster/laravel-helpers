<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Contracts\Support\Arrayable;

if (!function_exists('carbon')) {
    /**
     * @return \Carbon\Carbon|\Illuminate\Foundation\Application|mixed
     */
    function carbon()
    {
        return app(\Carbon\Carbon::class);
    }
}

/**
 * return column_{appLocale}
 */
if (!function_exists('columnLocalize')) {
    /**
     * Localize column name.
     *
     * @param string $columnName Column name
     * @param string|null $locale Locale name, Null = current locale name
     *
     * @return string
     */
    function columnLocalize($columnName = 'name', $locale = null)
    {
        return ltrim($columnName, '_') . '_' . ($locale ?: currentLocale());
    }
}

/**
 * return column_{appLocale}
 */
if (!function_exists('tool_title_locale')) {
    /**
     * return name_{appLocale}
     *
     * @return string
     */
    function tool_title_locale($column = 'name')
    {
        return columnLocalize($column);
    }
}

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

/**
 * return string
 */
if (!function_exists('prefixNumber')) {
    /**
     * like:
     * Number: 0001
     *
     * @param        $value
     * @param string $prefix
     * @param int $length
     *
     * @return string
     */
    function prefixNumber($value, $prefix = '0', $length = 4)
    {
        $prefix = trim($prefix ?: '0');
        return sprintf("%{$prefix}{$length}d", $value);
    }
}

/**
 * return string
 */
if (!function_exists('prefixText')) {
    /**
     * like:
     * Text:
     * ***id:
     *
     * @param        $value
     * @param string $prefix
     * @param int $length
     * @param int $pad_type [optional] <p>
     *                         Optional argument pad_type can be
     *                         STR_PAD_RIGHT, STR_PAD_LEFT,
     *                         or STR_PAD_BOTH. If
     *                         pad_type is not specified it is assumed to be
     *                         STR_PAD_BOTH.
     *                         </p>
     *
     * @return string
     */
    function prefixText($value, $prefix = ' ', $length = 10, $pad_type = STR_PAD_BOTH)
    {
        return str_pad($value, $length, $prefix ?: ' ', $pad_type);
    }
}

/**
 * return mixed
 */
if (!function_exists('replaceAll')) {
    /**
     * Replace a given data in string.
     *
     * @param Arrayable<mixed, mixed>|array<mixed, mixed> $searchAndReplace
     * @param string $subject
     * @return string
     */
    function replaceAll($searchAndReplace, $subject)
    {
        toCollect((array)$searchAndReplace)->each(function($replace, $search) use(&$subject) {
            $subject = str_ireplace($search, $replace, $subject);
        });

        return $subject;
    }
}

if (!function_exists('bindTo')) {
    /**
     * Bind the given Closure
     *
     * @param \Closure $closure
     * @param object|null $newthis The object to which the given anonymous function should be bound, or NULL for the closure to be unbound.
     * @param mixed $newscope The class scope to which associate the closure is to be associated, or 'static' to keep the current one.
     * If an object is given, the type of the object will be used instead.
     * This determines the visibility of protected and private methods of the bound object.
     *
     * @return Closure Returns the newly created Closure object
     */
    function bindTo($closure, $newthis = null, $newscope = 'static')
    {
        if(isClosure($closure) && $newthis) {
            return $closure->bindTo($newthis, $newscope);
        }

        return $closure;
    }
}
