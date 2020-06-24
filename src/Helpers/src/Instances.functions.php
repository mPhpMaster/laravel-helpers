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

if ( !function_exists('optionalGet') ) {
    /**
     * @param \Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Model|array|callable $object array example: [`class object`, `method name`]
     * @param callable|mixed|null                                                                                 $whenFalse
     * @param callable|mixed|null                                                                                 $whenTrue
     * @deprecated 
     * @return \Illuminate\Support\Optional|\Illuminate\Database\Eloquent\Relations\Relation|mixed|null
     */
    function optionalGet($object, $whenFalse = null, $whenTrue = null)
    {
        return optional($object);
        /**
         * @var \Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Model|array|callable|object|null $instance Class instance
         */
        $instance = null;

        /**
         * @var string|null $method_name Class method name to call
         */
        $method_name = null;
        /**
         * ## Object testing result.
         *
         * Values:
         * 0. **Has:** *No instance*, *no Method name*, *no Object*.
         * 1. **Has:** **Instance**, *no Method name*, **`Object is Instance`**.
         * 2. **Has:** **Instance**, **Method name**, **Object**.
         *
         * ---
         *
         * @var int
         */
        $object_context_count = 0;

        /**
         * assign the given object to global $instance, $method_name, $object_context_count, $object.
         *
         */
        $assign = function ($array) use (&$fetch, &$instance, &$method_name, &$object_context_count, &$object) {
            $array = value($array);
            if ( is_array($array) ) {
                [$instance, $method_name] = [
                    count($array) === 0 ? null : array_get($array, 0),
                    count($array) < 2   ? null : array_get($array, 1)
                ];

                $object_context_count = intval(!!($instance)) + intval(!!$method_name);
                return $array;
            }

            [$instance, $method_name] = [&$array, null];

            $object_context_count = intval(!!($instance)) + intval(!!$method_name);
            return $array;
        };

        /**
         * Try to explode the data from $object.
         *
         * @param mixed $array
         * @param bool $fetched
         *
         * @return array|callable|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation
         */
//        $fetch = function ($array, &$fetched = false) use (&$object, &$instance, &$method_name, &$assign, &$object_context_count) {
//            if ( $object_context_count !== 2 ) {
//
//            } else if( $object_context_count === 2 ) {
//                return $assign($array);
//            }
//
//            return [$array, $method_name];
//        };

        if ( isInstanceOf($object, Illuminate\Database\Eloquent\Relations\Relation::class) ) {
            dE(
                // get name
                $object->getRelation()
            );
            $assign([$object, 'getResults']);


            return tap($object->getResults(), function ($results) use ($method) {
                $this->setRelation($method, $results);
            });
        } else if ( isInstanceOf($object, \Illuminate\Database\Eloquent\Model::class) ) {
            $object = [$object, 'get'];
//            $object_context_count = 2;
        } else if ( is_callable($object) && !is_array($object) ) {
            $object = [$object, null];
//            $object_context_count = 1;
        } else {
            $object = [$object, null];
        }

        $assign($object);
        $result = null;
//        if( isInstanceOf([ [$instance, $method_name], $object], Illuminate\Database\Eloquent\Relations\Relation::class, function (&$_object, &$class) use(&$object, &$instance, &$method_name) {
//            return $object = [$instance = &$_object, &$method_name];
//        }) ) {
//        }
        if ( $instance && $method_name && is_object($instance) && method_exists($instance, $method_name) ) {
            $result = optional($instance)->$method_name();
        }

        if ( is_object($object) ) {
            if ( $method_name ) {
                if ( method_exists($object, $method_name) ) {
                    $result = optional($object)->$method_name();
                }
            }
        }

        if ( filled($result) ) {
            if ( !is_null($whenTrue) && is_callable($whenTrue) ) {
                return $whenTrue($result, /*[$instance, $method_name],*/ $instance);
            }

            return $result;
        }

        if ( !is_null($whenFalse) && is_callable($whenFalse) ) {
            return $whenFalse($result, $instance);
        }

        return optional($result);
//        $is_fetched = false;
//        [$instance, $method_name] = $fetch($object, $is_fetched);
//        throw_unless($is_fetched, new InvalidArgumentException(
//            "The given object must be an array with two elements, the first one for the Model|Instance, the second one for the method name."
//        ), [
//            $object,
//            [$instance, $method_name]
//        ]);
//
//        if ( filled($object) ) {
//            if ( !is_null($whenTrue) && is_callable($whenTrue) ) {
//                return $whenTrue([$instance, $object], $instance);
//            }
//
//            return $object;
//        }
//
//        if ( !is_null($whenFalse) && is_callable($whenFalse) ) {
//            return $whenFalse($object, $instance);
//        }
//
//        return optional($object);
    }
}
