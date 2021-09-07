<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Traits;

use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionMethod;


/**
 * Trait TMacroable
 *
 * @package MPhpMaster\LaravelHelpers\Traits
 */
trait TMacroable
{
    /**
     * @var string
     */
    public static $MACRO_NOT_FOUND = "MACRO_NOT_FOUND";

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
    public static function mixin($mixin, $replace = true)
    {
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            if ($replace || ! static::hasMacro($method->name)) {
                $method->setAccessible(true);
                static::macro($method->name, $method->invoke($mixin));
            }
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
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.', static::class, $method
            ));
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
        if ( ($result = $this->handleMacroCall($method, $parameters)) && $result !== static::$MACRO_NOT_FOUND ) {
            return $result;
        }

        if ( $result === static::$MACRO_NOT_FOUND ) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.', static::class, $method
            ));
        }

        return $result;
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function handleMacroCall($method, $parameters)
    {
        if ( !static::hasMacro($method) ) {
            return static::$MACRO_NOT_FOUND;
        }

        $macro = static::$macros[ $method ];

        if ( $macro instanceof Closure ) {
            return call_user_func_array($macro->bindTo($this, static::class), $parameters);
        }

        return call_user_func_array($macro, $parameters);
    }

}
