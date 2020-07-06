<?php
/**
 * Copyright (c) $year. By: hlaCk (https://github.com/mPhpMaster)
 *
 */

namespace mPhpMaster\Support\Traits;

use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionMethod;


/**
 * Trait TMacroable
 *
 * @package mPhpMaster\Support\Traits
 */
trait TMacroable
{
    /**
     * @var array
     */
    protected static $macros = [];

    /**
     * Register a custom macro.
     *
     * @param string          $name
     * @param object|callable $macro
     */
    public static function macro(string $name, $macro)
    {
        static::$macros[ $name ] = $macro;
    }

    /**
     * Mix another object into the class.
     *
     * @param object $mixin
     *
     * @throws \ReflectionException
     */
    public static function mixin($mixin)
    {
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            $method->setAccessible(true);

            static::macro($method->name, $method->invoke($mixin));
        }
    }

    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[ $name ]);
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        if ( !static::hasMacro($method) ) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        $macro = static::$macros[ $method ];

        if ( $macro instanceof Closure ) {
            return call_user_func_array(Closure::bind($macro, null, static::class), $parameters);
        }

        return call_user_func_array($macro, $parameters);
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ( !static::hasMacro($method) ) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        $macro = static::$macros[ $method ];

        if ( $macro instanceof Closure ) {
            return call_user_func_array($macro->bindTo($this, static::class), $parameters);
        }

        return call_user_func_array($macro, $parameters);
    }
}
