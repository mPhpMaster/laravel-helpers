<?php

use mPhpMaster\Support\Suffixer;
use mPhpMaster\Support\With;
use Illuminate\Support\Facades\Route;

if (!defined('e'))
    define('e', 'else');

//class isPlainVar { public function __construct($var="var") { $this->plain = $var; } };
if (!defined('UNUSED'))
    define('UNUSED', gzcompress(serialize(['plain' => 0x0011]), 9));
//    define('UNUSED', gzcompress(serialize(new isPlainVar('Variable')), 9));

//dd(UNUSED);
if (!function_exists('toLocaleDate')) {

    function toLocaleDate($date)
    {
        $ar = [
            "الأحد",
            "أح",
            "الإثنين",
            "إث",
            "الثلاثاء",
            "ث",
            "الأربعاء",
            "أر",
            "الخميس",
            "خ",
            "الجمعة",
            "ج",
            "السبت",
            "س",
            "ص",
            "ص",
            "م",
            "م",
            "يناير",
            "يناير",
            "فبراير",
            "فبراير",
            "مارس",
            "مارس",
            "أبريل",
            "أبريل",
            "مايو",
            "مايو",
            "يونيو",
            "يونيو",
            "يوليو",
            "يوليو",
            "أغسطس",
            "أغسطس",
            "سبتمبر",
            "سبتمبر",
            "اكتوبر",
            "اكتوبر",
            "نوفمبر",
            "نوفمبر",
            "ديسمبر",
            "ديسمبر",
        ];
        $notAr = [
            "Sunday",
            "Sun",
            "Monday",
            "Mon",
            "Tuesday",
            "Tue",
            "Wednesday",
            "Wed",
            "Thursday",
            "Thu",
            "Friday",
            "Fri",
            "Saturday",
            "Sat",
            "am",
            "AM",
            "pm",
            "PM",
            "January",
            "Jan",
            "February",
            "Feb",
            "March",
            "Mar",
            "April",
            "Apr",
            "May",
            "May",
            "June",
            "Jun",
            "July",
            "Jul",
            "August",
            "Aug",
            "September",
            "Sep",
            "October",
            "Oct",
            "November",
            "Nov",
            "December",
            "Dec",
        ];

//        $timestamp = strtotime('2019-01-01');
//        $months = [];
//
//        for ($i = 0; $i < 12; $i++) {
//            $months[] = strftime('%B', $timestamp);
//            $months[] = strftime('%b', $timestamp);
//            $timestamp = strtotime('+1 month', $timestamp);
//        }
//        dd($months);


        try {
            if (!app()->isLocale('ar') || !$date)
                return $date;


            return str_ireplace(
                $notAr,
                $ar,
                $date
            );
        } catch (\Exception $exception) {
            return $date;
        }
    }
}

if (!function_exists('globalCompacts')) {
    /**
     * get global vars to compact array.
     *
     * @return array
     */
    function globalCompacts()
    {
        global $auth_user;
        // Share user logged in
        $auth_user = AuthUser();
        return compact('auth_user');
    }
}

if (!function_exists('appendGlobalCompacts')) {
    /**
     * add global vars to compact array.
     *
     * @param array $compactValues
     *
     * @return array
     */
    function appendGlobalCompacts(array $compactValues)
    {
        return collect($compactValues ?: [])->merge(globalCompacts())->all();
    }
}

if (!function_exists('real_path')) {
    /**
     * return given path without ../
     *
     * @param null $path
     * @param string $DIRECTORY_SEPARATOR
     *
     * @return string
     */
    function real_path($path = null, $DIRECTORY_SEPARATOR = "/")
    {
        $_DIRECTORY_SEPARATOR = $DIRECTORY_SEPARATOR == "/" ? "\\" : "/";
        if ($path) $path = str_ireplace($_DIRECTORY_SEPARATOR, $DIRECTORY_SEPARATOR, $path);

        $backslash = "..{$DIRECTORY_SEPARATOR}";
        if (stripos($path, $backslash) !== false) {
            $path = collect(explode($backslash, $path))->reverse();
            $path = $path->map(function ($v, $i) use ($path) {
                $_v = dirname($v);
                return $i == $path->count() - 1 ? $v :
                    ($_v == '.' ? '' : $_v);
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

if (!function_exists('whenUsed')) {
    function whenUsed($var, callable $callable): bool
    {
        if ($var !== UNUSED) {
            $callable($var);
            return true;
        }

        return false;
    }
}

if (!function_exists('when')) {
    function when($cond, $then = null, $else = null)
    {
        $cond = !!value($cond);

        return $cond ? value($then) : value($else);
    }
}

if (!function_exists('makeWith')) {
    /**
     * @param mixed ...$value
     *
     * @return With
     */
    function makeWith(...$value)
    {
        return new With($value);
    }
}

#region IS
if (!function_exists('isUsedCount')) {
    function isUsedCount(...$var): int
    {
        $unUsedArgs = array_filter($var, function ($_var) {
            return isUsed($_var);
        });

        return count($unUsedArgs);
    }
}

if (!function_exists('isUsedAll')) {
    function isUsedAll(...$var): bool
    {
        return isUsedCount(...$var) === count($var);
    }
}

if (!function_exists('isUsedAny')) {
    function isUsedAny(...$var): bool
    {
        return isUsedCount(...$var) > 0;
    }
}

if (!function_exists('isUsed')) {
    function isUsed($var): bool
    {
        return $is_used = $var !== UNUSED;
    }
}

if (!function_exists('isPlain')) {
    function isPlain($var): bool
    {
        return $var === UNUSED;
    }
}

if (!function_exists('ifSet')) {
    function ifSet($var, $true = UNUSED, $false = UNUSED)
    {
        $true = isUsed($true) ? $true : (isset($var) ? $var : true);
        $false = isUsed($false) ? $false : null;

        return $var ? $true : $false;
//        return isset($var) ? $true : $false;
    }
}

if (!function_exists('firstSet')) {
    function firstSet(...$var)
    {
        foreach ($var as $_var)
            if (isset($_var))
                return $_var;

        return null;
    }
}

if (!function_exists('getAny')) {
    function &getAny(...$vars)
    {
        foreach ($vars as &$_var) {
            if ($_var) return $_var;
        }

        $null = null;
        return $null;
    }
}

if (!function_exists('test')) {
    function test(...$vars)
    {
        foreach ($vars as $_var)
            if ($_var = value($_var)) return $_var;

        return null;
    }
}

if (!function_exists('iif')) {
    /**
     * Test Condition and return on of two parameters
     *
     * @param mixed $var Condition
     *
     * @param mixed $true Return this if Condition == true
     * @param mixed $false Return this when Condition fail
     *
     * @return mixed
     */
    function iif($var, $true = true, $false = false)
    {
        return $var ? $true : $false;
    }
}

if (!function_exists('whenEmpty')) {
    /**
     * Apply the callback if the collection is empty.
     *
     * @param \Illuminate\Support\Collection|array|mixed $collection
     * @param callable|null $empty
     * @param callable|null $notEmpty
     *
     * @return mixed
     */
    function whenEmpty($collection, callable $empty, callable $notEmpty = null)
    {
        return toCollect($collection)->whenEmpty($empty, $notEmpty);
    }
}

if (!function_exists('whenNotEmpty')) {
    /**
     * Apply the callback if the collection is not empty.
     *
     * @param \Illuminate\Support\Collection|array|mixed $collection
     * @param callable|null $empty
     * @param callable|null $notEmpty
     *
     * @return mixed
     */
    function whenNotEmpty($collection, callable $notEmpty, callable $empty = null)
    {
        return toCollect($collection)->whenNotEmpty($empty, $notEmpty);
    }
}

#endregion

#region HAS
if (!function_exists('hasTrait')) {
    /**
     * Check if given class has trait.
     *
     * @param mixed $class <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $traitName <p>
     *                          Trait name to check
     *                          </p>
     *
     * @return bool
     */
    function hasTrait($class, $traitName)
    {
        try {
            $traitName = str_contains($traitName, "\\") ? class_basename($traitName) : $traitName;

            $hasTraitRC = new ReflectionClass($class);
            $hasTrait = collect($hasTraitRC->getTraitNames())->map(function ($name) use ($traitName) {
                    $name = str_contains($name, "\\") ? class_basename($name) : $name;

                    return $name == $traitName;
                })->filter()->count() > 0;
        } catch (ReflectionException $exception) {
            $hasTrait = false;
        } catch (Exception $exception) {
            d($exception->getMessage());
            $hasTrait = false;
        }

        return $hasTrait;
    }
}

if (!function_exists('hasKey')) {
    /**
     * Check if given array has key if has key call $callable.
     *
     * @param array $array
     * @param string $key
     * @param Closure|null $callable
     *
     * @return bool|mixed
     */
    function hasKey($array, $key, Closure $callable = null)
    {
        try {
            $has = array_key_exists($key, $array);
            if ($callable && is_callable($callable)) {
                return $callable->call($array, $array);
            }

            return $has === true;
        } catch (Exception $exception) {
            d($exception->getMessage());
            $hasTrait = false;
        }

        return false;
    }
}

if (!function_exists('hasScope')) {
    /**
     * Check if given class has the given scope name.
     *
     * @param mixed $class <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $scopeName <p>
     *                          Scope name to check
     *                          </p>
     *
     * @return bool
     */
    function hasScope($class, $scopeName)
    {
        try {
            $hasScopeRC = new ReflectionClass($class);
            $scopeName = strtolower(studly_case($scopeName));
            $scopeName = starts_with($scopeName, "scope") ? substr($scopeName, strlen("scope")) : $scopeName;

            $hasScope = collect($hasScopeRC->getMethods())->map(function ($c) use ($scopeName) {
                    /**
                     * @var $c ReflectionMethod
                     */

                    $name = strtolower(studly_case($c->getName()));
                    $name = starts_with($name, "scope") ? substr($name, strlen("scope")) : false;

                    return $name == $scopeName;
                })->filter()->count() > 0;
        } catch (ReflectionException $exception) {
            $hasScope = false;
        } catch (Exception $exception) {
            $hasScope = false;
        }

        return !!$hasScope;
    }
}

if (!function_exists('hasConst')) {
    /**
     * Check if given class has the given const.
     *
     * @param mixed $class <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $const <p>
     *                          Const name to check
     *                          </p>
     *
     * @return bool
     */
    function hasConst($class, $const): bool
    {
        $hasScope = false;
        try {
            if (is_object($class) || is_string($class)) {
                $reflect = new ReflectionClass($class);
                $hasScope = array_key_exists($const, $reflect->getConstants());
            }
        } catch (ReflectionException $exception) {
            $hasScope = false;
        } catch (Exception $exception) {
            $hasScope = false;
        }

        return (bool)$hasScope;
    }
}
#endregion

#region CURRENT
if (!function_exists('currentController')) {
    /**
     * @return \Illuminate\Routing\Controller|null
     */
    function currentController()
    {
        $route = Route::current();
        if (!$route) return null;

        if (isset($route->controller) || method_exists($route, 'getController')) {
            return isset($route->controller) ? $route->controller : $route->getController();
        }

        $action = $route->getAction();
        if ($action && isset($action['controller'])) {
            $currentAction = $action['controller'];
            list($controller, $method) = explode('@', $currentAction);
            return $controller ? app($controller) : $controller;
        }

        return null;
    }
}

if (!function_exists('currentRoute')) {
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

if (!function_exists('currentNamespace')) {
    /**
     * Returns namespace of current controller
     *
     * @return null|string Namespace
     */
    function currentNamespace()
    {
        try {
            $currentController = currentController();
            if ($currentController && (
                    (is_string($currentController) && class_exists($currentController)) ||
                    is_object($currentController)
                )) {
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
#endregion

#region GET
if (!function_exists('getRequestedPage')) {
    /**
     * Returns page from request
     *
     * @return int|bool
     */
    function getRequestedPage()
    {
        if (!request()->has('page')) return false;

        $page = request()->get('page', 1);
        return strtolower($page) === 'all' ? 0 : $page;
    }
}

if (!function_exists('getMethodName')) {
    /**
     * Returns method name by given Route->uses
     *
     * @param string $method
     *
     * @return string
     */
    function getMethodName(string $method)
    {
        if (empty($method)) return '';

        if (stripos($method, '::') !== false)
            $method = collect(explode('::', $method))->last();

        if (stripos($method, '@') !== false)
            $method = collect(explode('@', $method))->last();

        return $method;
    }
}

if (!function_exists('getCurrentNamespace')) {
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
        $caller = $caller[$backtrace_times];
        $class = null;
        try {
            if (isset($caller['class'])) {
                $class = (new ReflectionClass($caller['class']))->getNamespaceName();
            }
            if (isset($caller['object'])) {
                $class = (new ReflectionClass(get_class($caller['object'])))->getNamespaceName();
            }
        } catch (ReflectionException $exception) {
//			d($exception);
            return null;
        }
        if ($append) $append = str_ireplace("/", "\\", $append);
        if ($class) $class = str_ireplace("/", "\\", $class);

        if ($class) $class = real_path("{$class}" . ($append ? "\\{$append}" : ""));

        return $class;
    }
}

if (!function_exists('getControllerPermissionPrefix')) {
    /**
     * Returns prefix of permissions name
     *
     * @param \Illuminate\Routing\Controller|string|null $controller Controller or controller name, default: {@see currentController()}
     * @param string|null $permission_name Permission name
     * @param string $separator Permission name separator
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

if (!function_exists('suffixerMaker')) {
    /**
     * Alias for: {@link Suffixer::makeer}
     *
     * @return Closure
     */
    function suffixerMaker(): Closure
    {
        return Suffixer::makeer(...func_get_args());
    }
}
if (!function_exists('str_prefix')) {
    /**
     * Add a prefix to string but only if string2 is not empty.
     *
     * @param string $string string to prefix
     * @param string $prefix prefix
     * @param string|null $string2 string2 to prefix the return
     *
     * @return string|null
     */
    function str_prefix($string, $prefix, $string2 = null)
    {
        $newString = rtrim(is_null($string2) ? '' : $string2, $prefix) .
            $prefix .
            ltrim($string, $prefix);

        return ltrim($newString, $prefix);
    }
}
if (!function_exists('str_suffix')) {
    /**
     * Add a suffix to string but only if string2 is not empty.
     *
     * @param string $string string to suffix
     * @param string $suffix suffix
     * @param string|null $string2 string2 to suffix the return
     *
     * @return string|null
     */
    function str_suffix($string, $suffix, $string2 = null)
    {
        $newString = ltrim($string, $suffix) . $suffix . rtrim(is_null($string2) ? '' : $string2, $suffix);

        return trim($newString, $suffix);
    }
}

if (!function_exists('str_words_limit')) {
    /**
     * Limit string words.
     *
     * @param string $string string to limit
     * @param int $limit word limit
     * @param string|null $suffix suffix the string
     *
     * @return string
     */
    function str_words_limit($string, $limit, $suffix = '...')
    {
        $start = 0;
        $stripped_string = strip_tags($string); // if there are HTML or PHP tags
        $string_array = explode(' ', $stripped_string);
        $truncated_array = array_splice($string_array, $start, $limit);

        $lastWord = end($truncated_array);
        $return = substr($string, 0, stripos($string, $lastWord) + strlen($lastWord)) . ' ' . $suffix;

        $m = [];
        if (preg_match_all('#<(\w+).+?#is', $return, $m)) {
            $m = is_array($m) && is_array($m[1]) ? array_reverse($m[1]) : [];
            foreach ($m as $HTMLTAG) {
                $return .= "</{$HTMLTAG}>";
            }
        }

        return $return;
    }
}

if (!function_exists('basenameOf')) {
    /**
     * Returns basename of the given string after replace slashes and back slashes to DIRECTORY_SEPARATOR
     * @param string $string
     *
     * @return string
     */
    function basenameOf(string $string)
    {
        $string = replaceAll([
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        ], $string);

        return basename($string);
    }
}
#endregion