<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
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

if ( !function_exists('isRunningInConsole') ) {
    /**
     * @return bool
     * @noinspection ForgottenDebugOutputInspection
     */
    function isRunningInConsole()
    {
        static $runningInConsole = null;

        if ( isset($_ENV['APP_RUNNING_IN_CONSOLE']) || isset($_SERVER['APP_RUNNING_IN_CONSOLE']) ) {
            return ($runningInConsole = $_ENV['APP_RUNNING_IN_CONSOLE']) ||
                ($runningInConsole = $_SERVER['APP_RUNNING_IN_CONSOLE']) === 'true';
        }

        return $runningInConsole = $runningInConsole ?: (
            \Illuminate\Support\Env::get('APP_RUNNING_IN_CONSOLE') ??
            (\PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg' || in_array(php_sapi_name(), ['cli', 'phpdb']))
        );
    }
}


if ( !function_exists('whenRunningInConsole') ) {
    /**
     * return first argument if user is logged in otherwise return second argument.
     *
     * @return mixed
     */
    function whenRunningInConsole(callable $when_true = null, callable $when_false = null)
    {
        return is_callable($value = $isRunningInConsole = isRunningInConsole() ? $when_true : $when_false) ?
            call_user_func_array($value, [$isRunningInConsole, User()]) :
            $value;
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

if ( !function_exists('stringContainsAll') ) {
    /**
     * Determine if a given string contains all array values.
     *
     * @param string   $haystack
     * @param string[] $needles
     *
     * @return bool
     */
    function stringContainsAll($haystack, array $needles)
    {
        foreach ($needles as $needle) {
            if ( !stringContains($haystack, $needle) ) {
                return false;
            }
        }

        return true;
    }
}

if ( !function_exists('stringContainsAll') ) {
    /**
     * Determine if a given string contains all array values.
     *
     * @param string   $haystack
     * @param string[] $needles
     *
     * @return bool
     */
    function stringContainsAll($haystack, array $needles)
    {
        foreach ($needles as $needle) {
            if ( !stringContains($haystack, $needle) ) {
                return false;
            }
        }

        return true;
    }
}

if ( !function_exists('stringContains') ) {
    /**
     * Determine if a given string contains a given substring.
     *
     * @param string          $haystack
     * @param string|string[] $needles
     * @param bool            $ignore_case
     *
     * @return bool
     */
    function stringContains(string $haystack, $needles, $ignore_case = false)
    {
        foreach ((array)$needles as $needle) {
            if ( $ignore_case ) {
                $needle = snake_case($needle);
                $haystack = snake_case($haystack);
            }

            if ( is_string($haystack) && is_string($needle) && $needle !== '' && mb_strpos($haystack, $needle) !== false ) {
                return true;
            }
        }

        return false;
    }
}

if ( !function_exists('stringEnds') ) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string          $haystack
     * @param string|string[] $needles
     *
     * @return bool
     */
    function stringEnds($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ( $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle ) {
                return true;
            }
        }

        return false;
    }

}

if ( !function_exists('stringStarts') ) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string          $haystack
     * @param string|string[] $needles
     *
     * @return bool
     */
    function stringStarts($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ( (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0 ) {
                return true;
            }
        }

        return false;
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

if ( !function_exists('notify') ) {
    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param \App\Models\AppModel|\Illuminate\Support\Collection|array|mixed $notifiables
     * @param mixed                                                           $notification
     *
     * @return \Illuminate\Contracts\Bus\Dispatcher|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function notify($notifiables, $notification)
    {
        return dispatcher(\Illuminate\Contracts\Notifications\Dispatcher::class)->send($notifiables, $notification);
    }
}

if ( !function_exists('notifyNow') ) {
    /**
     * Send the given notification to the given notifiable entities immediately.
     *
     * @param \App\Models\AppModel|\Illuminate\Support\Collection|array|mixed $notifiables
     * @param mixed                                                           $notification
     *
     * @return \Illuminate\Contracts\Bus\Dispatcher|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function notifyNow($notifiables, $notification)
    {
        return dispatcher()->sendNow($notifiables, $notification);
    }
}

if ( !function_exists('appDispatch') ) {
    /**
     * @param        $command
     * @param string $dispatcher_class
     *
     * @return \Illuminate\Contracts\Bus\Dispatcher|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function appDispatch($command, $dispatcher_class = \Illuminate\Bus\Dispatcher::class)
    {
        $dispatcher = dispatcher($dispatcher_class);
        return $command ? $dispatcher->dispatch($command) : $dispatcher;
    }
}

if ( !function_exists('dispatcher') ) {
    /**
     * @param string $dispatcher_class
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Bus\Dispatcher|mixed
     */
    function dispatcher($dispatcher_class = \Illuminate\Contracts\Bus\Dispatcher::class)
    {
        return app($dispatcher_class ?: \Illuminate\Contracts\Bus\Dispatcher::class);
    }
}
