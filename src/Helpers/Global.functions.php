<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Facades\Route;

// region: to
if ( !function_exists('toCollect') ) {
    /**
     * Returns $var as collection
     *
     * @param $var
     *
     * @return \Illuminate\Support\Collection
     */
    function toCollect($var): \Illuminate\Support\Collection
    {
        return is_collection($var) ? $var : collect($var);
    }
}

if ( !function_exists('toCollectWithModel') ) {
    /**
     * Returns $var as collection, if the given var is model ? return collect([model])
     *
     * @param $var
     *
     * @return \Illuminate\Support\Collection
     */
    function toCollectWithModel($var): \Illuminate\Support\Collection
    {
        $var = $var instanceof \Illuminate\Database\Eloquent\Model ? [$var] : $var;
        return toCollect($var);
    }
}

if ( !function_exists('toCollectOrModel') ) {
    /**
     * Returns $var as collection, if the given var is model ? return model
     *
     * @param $var
     *
     * @return \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model
     */
    function toCollectOrModel($var)
    {
        return is_collection($var) || $var instanceof \Illuminate\Database\Eloquent\Model ? $var : collect($var);
    }
}

if ( !function_exists('toBoolValue') ) {
    /**
     * Returns value as boolean
     *
     * @param $var
     *
     * @return bool
     */
    function toBoolValue($var): bool
    {
        if ( is_bool($var) ) return boolval($var);

        !is_bool($var) && ($var = strtolower(trim($var)));
        !is_bool($var) && ($var = $var === 'false' ? false : $var);
        !is_bool($var) && ($var = $var === 'true' ? true : $var);
        !is_bool($var) && ($var = $var === '1' ? true : $var);
        !is_bool($var) && ($var = $var === '0' ? false : $var);

        return boolval($var);
    }
}
// endregion: to

// region: is
if ( !function_exists('is_collection') ) {
    /**
     * @param $var
     *
     * @return bool
     */
    function is_collection(&$var): bool
    {
        return $var instanceof \Illuminate\Support\Collection;
    }
}

if ( !function_exists('isLoggedIn') ) {
    /**
     * check if user is logged in.
     *
     * @return bool
     */
    function isLoggedIn()
    {
        return !!Auth::check();
    }
}

if ( !function_exists('whenLoggedIn') ) {
    /**
     * return first argument if user is logged in otherwise return second argument.
     *
     * @return mixed
     */
    function whenLoggedIn(callable $when_true = null, callable $when_false = null)
    {
        return getValue($isLoggedIn = isLoggedIn() ? $when_true : $when_false, $isLoggedIn, User());
    }
}

if ( !function_exists('isViewMode') ) {
    /**
     * get current route
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function isViewMode($mode)
    {
        return strtolower(trim($mode)) == strtolower(trim(ViewMode()));
    }
}
// endregion: is

// region: current
if ( !function_exists('currentNamespace') ) {
    /**
     * Returns namespace of current controller
     *
     * @return null|string Namespace
     */
    function currentNamespace()
    {
        try {
            $currentController = currentController();
            if ( $currentController && (
                    (is_string($currentController) && class_exists($currentController)) ||
                    is_object($currentController)
                ) ) {
                $class = get_class($currentController);
                $namespace = (new ReflectionClass($class))->getNamespaceName();
            } else {
                return null;
            }
        } catch (ReflectionException $exception) {
            return null;
        }

        return $namespace;
    }
}

if ( !function_exists('currentRoute') ) {
    /**
     * Returns current route
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function currentRoute()
    {
        $route = Route::current();
        $route = $route ?: app(Route::class);

        return $route;
    }
}

if ( !function_exists('currentController') ) {
    /**
     * @return \Illuminate\Routing\Controller|null
     */
    function currentController()
    {
        $route = Route::current();
        if ( !$route ) return null;

        if ( isset($route->controller) || method_exists($route, 'getController') ) {
            return isset($route->controller) ? $route->controller : $route->getController();
        }

        $action = $route->getAction();
        if ( $action && isset($action['controller']) ) {
            $currentAction = $action['controller'];
            list($controller, $method) = explode('@', $currentAction);
            return $controller ? app($controller) : $controller;
        }

        return null;
    }
}

if ( !function_exists('currentMethod') ) {
    /**
     * @param null $method
     *
     * @return string
     */
    function currentMethod($method = null)
    {
        $method = $method ?: currentActionName();
        return (string)$method;
    }
}

if ( !function_exists('currentModelName') ) {
    /**
     * @param null $method
     *
     * @return string
     */
    function currentModelName($model = null)
    {
        $model = $model ?: iif(
            ($controller = currentController()),
            str_before(class_basename($controller), "Controller"),
            ""
        );
        return (string)$model;
    }
}
// endregion: current

if ( !function_exists('collectGet') ) {
    /**
     * Returns value from collection by key
     *
     * @param        $collect
     * @param        $key
     * @param string $default
     *
     * @return mixed
     */
    function collectGet($collect, $key, $default = UNUSED)
    {
        return toCollect($collect)->get($key, $default);
    }
}

if ( !function_exists('boolval') ) {
    /**
     * Get the boolean value of a variable
     *
     * @param mixed The scalar value being converted to a boolean.
     *
     * @return boolean The boolean value of var.
     */
    function boolval($var)
    {
        return !!$var;
    }
}

if ( !function_exists('real_path') ) {
    /**
     * return given path without ../
     *
     * @param null   $path
     * @param string $DIRECTORY_SEPARATOR
     *
     * @return string
     */
    function real_path($path = null, $DIRECTORY_SEPARATOR = "/")
    {
        $_DIRECTORY_SEPARATOR = $DIRECTORY_SEPARATOR === "/" ? "\\" : "/";
        if ( $path ) $path = str_ireplace($_DIRECTORY_SEPARATOR, $DIRECTORY_SEPARATOR, $path);

        $backslash = "..{$DIRECTORY_SEPARATOR}";
        if ( stripos($path, $backslash) !== false ) {
            $path = collect(explode($backslash, $path))->reverse();
            $path = $path->map(function ($v, $i) use ($path) {
                $_v = ($_v = dirname($v)) === '.' ? '' : $_v;
                return $i == $path->count() - 1 ? $v : $_v;
            });
            $path = str_ireplace(
                $DIRECTORY_SEPARATOR . $DIRECTORY_SEPARATOR,
                $DIRECTORY_SEPARATOR,
                $path->reverse()->implode($DIRECTORY_SEPARATOR)
            );
        }

        return collect($path)->first();
    }
}

if ( !function_exists('ViewMode') ) {
    /**
     * get current route
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function ViewMode()
    {
        try {
            $array = explode('.', CurrentRoute()->getName());
            return @end($array);
        } catch (Exception $exception) {
            return null;
        }
    }
}

if ( !function_exists('appDispatch') ) {
    /**
     * Send the given command to the dispatcher for execution.
     *
     * @param object $command
     *
     * @return void
     */
    function appDispatch($command)
    {
        return app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch($command);
    }
}
