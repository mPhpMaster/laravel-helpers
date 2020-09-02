<?php /** @noinspection ForgottenDebugOutputInspection */

/**
 * Created by PhpStorm.
 * User: MyTh
 * Date: 16/4/2019
 * Time: 7:42 AM
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;

/**
 * return column_{appLocale}
 */
if ( !function_exists('tool_title_locale') ) {
    /**
     * return name_{appLocale}
     *
     * @return string
     */
    function tool_title_locale($column = 'name')
    {
        return ltrim($column, '_') . '_' . app()->getLocale();
    }
}

/**
 * return table name}
 */
if ( !function_exists('getTable') ) {
    /**
     * Returns Model table name.
     *
     * @param string $model Model class.
     *
     * @return null|string
     */
    function getTable(string $model)
    {
        if ( $model && class_exists($model) ) {
            $class = new $model;

            /** @var $class \Illuminate\Database\Eloquent\Model */
            return $class->getTable();
        }

        return null;
    }
}

/**
 * return class methods}
 */
if ( !function_exists('getMethods') ) {
    /**
     * Returns Model methods list.
     *
     * @param mixed $model Model class.
     *
     * @return null|array|\Illuminate\Support\Collection
     */
    function getMethods($model)
    {
        return get_class_methods($model);
    }
}

/**
 * return model fillable}
 */
if ( !function_exists('getFillable') ) {
    /**
     * Returns Model Fillable.
     *
     * @param string $model Model class.
     *
     * @return null|array
     */
    function getFillable(string $model)
    {
        if ( $model && class_exists($model) ) {
            $class = new $model;
            /** @var $class \Illuminate\Database\Eloquent\Model */
            return $class->getFillable();
        }

        return null;
    }
}

if ( !function_exists('getTrans') ) {
    /**
     * Returns Translation or return default.
     *
     * @param string|null $lang_path lang path
     * @param null|mixed  $default default value to return if trans not exists
     *
     * @return mixed
     */
    function getTrans($lang_path, $default = null)
    {
        $trans = ($trans = __($lang_path)) != $lang_path ? $trans : $default;

        return $trans;
    }
}

if ( !function_exists('cutBasePath') ) {
    /**
     * Remove base_path() from the given file path.
     *
     * @param string $_fullFilePath file path
     * @param string $prefix any text to prefix the result with.
     *
     * @return string
     */
    function cutBasePath($_fullFilePath = null, $prefix = '')
    {
        $fullFilePath = $_fullFilePath ?:
            array_get(
                head(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)),
                'file',
                null
            );

        return $prefix . str_ireplace(
                fixPath(base_path() . DIRECTORY_SEPARATOR),
                '',
                fixPath($fullFilePath ?: __FILE__)
            );
    }
}

if ( !function_exists('classPropertyValue') ) {
    /**
     * Get property value fom class
     *
     * @param string $class
     * @param string $property
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    function classPropertyValue(string $class, string $property)
    {
        $_property = new ReflectionProperty($class, $property);
        $_property->setAccessible(true);
        return $_property->getValue();
    }
}

if ( !function_exists('getArrayFirst') ) {
    /**
     * Get first item in the given array as [ $value, $key ]
     *
     * @param iterable      $array
     * @param callable|null $callback
     *
     * @return mixed
     */
    function getArrayFirst(iterable $array, callable $callback = null)
    {
        try {
            if ( !is_array($array) ) {
                $array = valueToArray($array);
            }
            reset($array);

            $key = key($array);
            $value = current($array);
        } catch (Exception | Error $exception) {
            $fresh = 0;

            foreach ($array as $k => $v) {
                if ( $fresh === 1 ) {
                    $key = $k;
                    $value = $v;
                    $fresh++;
                } else {
                    break;
                }
            }
        }

        $_row = [
            $value,
            $key,
        ];
        $callback = is_callable($callback) ? $callback : function ($value, $key) {
            return [$value, $key];
        };

        return is_callable($callback) ? $callback(...$_row) : $_row;
    }
}

if ( !function_exists('Row') ) {
    /**
     * Get first item in the given array as [ $value, $key ], then remove it from array.
     *
     * @param array         $array
     * @param callable|null $callback
     *
     * @return array|mixed
     */
    function Row(array &$array, callable $callback = null)
    {
        $row = getArrayFirst(...func_get_args());

        if ( !is_array($array) ) {
            $array = valueToArray($array);
        }

        if ( is_array($row) && is_array($array) ) {
            [, $index] = $row;
            array_has($array, $index) && array_forget($array, $index);
        }

        return $row;
    }
}

if ( !function_exists('getNumbers') ) {
    /**
     * Returns Numbers only from the given string
     *
     * @param $string
     *
     * @return string
     */
    function getNumbers($string)
    {
        return preg_filter("/[^0-9]+/", "", $string);
    }
}

if ( !function_exists('getClass') ) {
    /**
     * Returns the name of the class of an object
     *
     * @param object $object |string [optional] <p> The tested object. This parameter may be omitted when inside a class. </p>
     *
     * @return string|false <p> The name of the class of which <i>`object`</i> is an instance.</p>
     * <p>
     *      Returns <i>`false`</i> if <i>`object`</i> is not an <i>`object`</i>.
     *      If <i>`object`</i> is omitted when inside a class, the name of that class is returned.
     * </p>
     */
    function getClass($object)
    {
        if ( is_object($object) ) {
            return get_class(valueToObject($object));
        }

        return $object && is_string($object) && class_exists($object) ? $object : false;
    }
}

if ( !function_exists('getRealClassName') ) {
    /**
     * Returns the real class name.
     *
     * @param string|object $class <p> The tested class. This parameter may be omitted when inside a class. </p>
     *
     * @return string|false <p> The name of the class of which <i>`class`</i> is an instance.</p>
     * <p>
     *      Returns <i>`false`</i> if <i>`class`</i> is not an <i>`class`</i>.
     *      If <i>`class`</i> is omitted when inside a class, the name of that class is returned.
     * </p>
     */
    function getRealClassName($class)
    {
        if ( is_object($class) ) {
            $class = get_class($class);
        }
        throw_if(!class_exists($class), new Exception("Class `{$class}` not exists!"));

        try {
            $_class = eval("return new class extends {$class} { public function getResourceValueAttribute(): string { return \"\"; } };");

        } catch (Exception $exception) {
            dE(
                $exception->getMessage()
            );
        }

        if ( $_class && is_object($_class) ) {
            return get_parent_class($_class);
        }

        return false;
    }
}

if ( !function_exists("filesMap") ) {
    /**
     * Get Files names into collect()->mapWithKeys()->filter()->toArray() list as [ FilenameWithoutExtension => $callabke(RealPath) ]
     *
     * @param               $path
     * @param callable|null $callback
     * @param null          $default
     *
     * @return null
     */
    function filesMap($path, callable $callback = null, $default = null)
    {
        $data = null;
        $path = (new Filesystem)->exists($path) ? $path : null;

        if ( $path ) {
            $data = collect((new Filesystem())->files($path))->mapWithKeys(function ($v) use ($callback) {
                /** @var $v \Symfony\Component\Finder\SplFileInfo */
                $map = [pathinfo($v->getFilename(), PATHINFO_FILENAME) => $v->getRealPath()];
                return is_callable($callback) ? $callback($map) : $map;
            })->filter()->toArray();
        }

        return iif($data, $data, $default) ?: null;
    }
}

if ( !function_exists("getByKey") ) {
    /**
     * @param      $data
     * @param null $key
     *
     * @return array|mixed|null
     */
    function getByKey($data, $key = null)
    {
        if ( is_null($key) ) {
            return $data;
        }
        $data = valueToArray($data ?: []);

        if ( $key && array_has($data, $key) ) {
            $data = array_get($data, $key, []);
        }

        return $data;
    }
}

if ( !function_exists('getOld') ) {
    /**
     * Retrieve an old input item.
     *
     * @param string|null $key
     * @param Model|null  $model
     * @param mixed       $default
     *
     * @return mixed
     */
    function getOld($key, $model = null, $default = null)
    {
        $model = test($model, currentModel());
        $old = old($key, $model ? $model->{$key} : $default);

        return is_null($model) ? $default : trim($old);
    }
}

if ( !function_exists('slice') ) {
    /**
     * Slice the given array.
     *
     * @param array    $items
     * @param int      $offset
     * @param int|null $length
     *
     * @return array
     */
    function slice(array $items, $offset, $length = null)
    {
        $items = valueToArray($items);
        return array_slice($items, $offset, $length, true);
    }
}

if ( !function_exists('take') ) {
    /**
     * Take the first or last {$limit} items.
     *
     * @param array $items
     * @param int   $limit
     *
     * @return array
     */
    function take(array $items, $limit)
    {
        if ( $limit < 0 ) {
            return slice($items, $limit, abs($limit));
        }

        return slice($items, 0, $limit);
    }
}

if ( !function_exists('getFirst') ) {
    /**
     * @param mixed ...$vars
     *
     * @return mixed|null
     */
    function getFirst(...$vars)
    {
        foreach ($vars as $_var) {
            if ( $_var ) {
                return $_var;
            }
        }

        return null;
    }
}

if ( !function_exists('getFirstValueByKey') ) {
    /**
     * Get first existing key from object.
     *
     * @param \Illuminate\Support\Collection|array|mixed           $request
     * @param \Illuminate\Contracts\Support\Arrayable|array|string $keys
     * @param mixed|null                                           $default
     *
     * @return mixed
     */
    function getFirstValueByKey($request, $keys, $default = null)
    {
        $request = toCollect($request);
        $return = $default;
        toCollect($keys)->each(function ($key) use (&$return, $request) {
            if ( $request->has($key) ) {
                $return = $request->get($key);
                return false;
            }
            return true;
        });

        return $return;
    }
}

if ( !function_exists('getFirstKeyByKey') ) {
    /**
     * Get first existing key from object.
     *
     * @param \Illuminate\Support\Collection|array|mixed           $request
     * @param \Illuminate\Contracts\Support\Arrayable|array|string $keys
     * @param mixed|null                                           $default
     *
     * @return mixed
     */
    function getFirstKeyByKey($request, $keys, $default = null)
    {
        $request = toCollect($request);
        $return = $default;
        toCollect($keys)->each(function ($key) use (&$return, $request) {
            if ( $request->has($key) ) {
                $return = $key;
                return false;
            }
            return true;
        });

        return $return;
    }
}

if ( !function_exists('getRequestedPage') ) {
    /**
     * @param int                           $default
     * @param \Illuminate\Http\Request|null $request
     *
     * @return int|null
     */
    function getRequestedPage(int $default = 0, \Illuminate\Http\Request &$request = null)
    {
        $request ??= request();
        if ( !$request->has('page') ) {
            return $default ?? null;
        }

        $page = $request->get('page', $default);
        return strtolower($page) === 'all' ? 0 : $page;
    }
}

if ( !function_exists('getRequestedPageCount') ) {
    /**
     * @param int                           $default
     * @param \Illuminate\Http\Request|null $request
     * @param string                        $key
     *
     * @return int|null
     */
    function getRequestedPageCount(int $default = null, \Illuminate\Http\Request &$request = null, $key = "itemsPerPage")
    {
        $request ??= request();
        $default ??= config('app.per_page', null);
        if ( !$request->has($key) ) {
            return $default;
        }

        $itemsPerPage = $request->get($key, $default);
        return strtolower($itemsPerPage) === 'all' ? -1 : $itemsPerPage;
    }
}

if ( !function_exists('getMethodName') ) {
    /**
     * Returns method name by given Route->uses
     *
     * @param string $method
     *
     * @return string
     */
    function getMethodName(string $method)
    {
        if ( empty($method) ) return '';

        if ( stripos($method, '::') !== false )
            $method = collect(explode('::', $method))->last();

        if ( stripos($method, '@') !== false )
            $method = collect(explode('@', $method))->last();

        return $method;
    }
}

if ( !function_exists('getCurrentNamespace') ) {
    /**
     * Returns current namespace of current class|object
     *
     * @param null $append
     *
     * @return null|string
     */
    function getCurrentNamespace($append = null, $backtrace_times = 1)
    {
        $caller = debug_backtrace();
        $caller = $caller[ $backtrace_times ];
        $class = null;
        try {
            if ( isset($caller['class']) ) {
                $class = (new ReflectionClass($caller['class']))->getNamespaceName();
            }
            if ( isset($caller['object']) ) {
                $class = (new ReflectionClass(get_class($caller['object'])))->getNamespaceName();
            }
        } catch (ReflectionException $exception) {
//			d($exception);
            return null;
        }
        if ( $append ) $append = str_ireplace("/", "\\", $append);
        if ( $class ) $class = str_ireplace("/", "\\", $class);

        if ( $class ) $class = real_path("{$class}" . ($append ? "\\{$append}" : ""));

        return $class;
    }
}

if ( !function_exists('getControllerPermissionPrefix') ) {
    /**
     * Returns prefix of permissions name
     *
     * @param \Illuminate\Routing\Controller|string|null $controller Controller or controller name, default: {@see currentController()}
     * @param string|null                                $permission_name Permission name
     * @param string                                     $separator Permission name separator
     *
     * @return string
     */
    function getControllerPermissionPrefix($controller = null, $permission_name = null, $separator = "_"): string
    {
        $controller = $controller instanceof \Illuminate\Routing\Controller ? get_class($controller) : ($controller ? trim($controller) : get_class(currentController()));

        $controller = str_before(class_basename($controller), "Controller");

        $controller .= $permission_name ? ucfirst($permission_name) : '';

        $controller = snake_case($controller);

        $controller = $permission_name ? $controller : str_finish($controller, "_");

        return str_ireplace("_", $separator, $controller);
    }
}
