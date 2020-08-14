<?php
/**
 * Copyright (c) $year. By: hlaCk (https://github.com/mPhpMaster)
 */

if ( !function_exists('carbon') ) {
    /**
     * @return \Carbon\Carbon|\Illuminate\Foundation\Application|mixed
     */
    function carbon()
    {
        return app(\Carbon\Carbon::class);
    }
}

if ( !function_exists('newMe') ) {
    /**
     * Returns new instance of class that you called this function from
     * # NOTE: use inside class only #
     *
     * @param mixed ...$arguments to send to new instance
     *
     * @return mixed
     * @throws \Exception|\Throwable
     */
    function newMe(...$arguments)
    {

        $currentDebug = collect(debug_backtrace())->get(1);
        $class = $currentDebug['class'] ?? false;

        throw_unless($class && class_exists($class),
            \Symfony\Component\ErrorHandler\Error\ClassNotFoundError::class,
            [["Last called class not found! [{$class}]", $currentDebug], null]
        );

        return new $class(...func_get_args());
    }
}

if ( !function_exists('newInstance') ) {
    /**
     * Returns new instance of the given classInstance/className with the given arguments
     *
     * @param string|object $class
     * @param mixed         ...$arguments to send to new instance
     *
     * @return mixed
     * @throws \Throwable
     */
    function newInstance($class, ...$arguments)
    {
        throw_if(
            !$class || (is_string($class) && !class_exists($class)),
            \Symfony\Component\ErrorHandler\Error\ClassNotFoundError::class,
            ["Class not exists! [{$class}]", null]
        );

        if ( is_object($class) ) {
            $class = get_class($class);
        }

        if ( $class ) {
            return (new $class(...$arguments));
        }

        return $class;
    }
}

if ( !function_exists('proxy') ) {
    /**
     * create HigherOrderProxy proxy.
     *
     * @param string|object|callable $class
     * @param string|null            $getMethod
     * @param string|null            $callMethod
     *
     * @return \HigherOrderProxy
     */
    function proxy($class, ?string $getMethod = null, ?string $callMethod = null)
    {
        return new HigherOrderProxy($class, $getMethod, $callMethod);
    }
}
