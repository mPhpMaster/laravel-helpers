<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Arr;
use mPhpMaster\Support\Suffixer;
use Symfony\Component\VarDumper\VarDumper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as ModelAuthUser;
use Illuminate\Support\Facades\Route;

// region: with tools

if (!defined('e'))
    define('e', 'else');

//class isPlainVar { public function __construct($var="var") { $this->plain = $var; } };
if (!defined('UNUSED'))
    define('UNUSED', gzcompress(serialize(['plain' => 0x0011]), 9));
//    define('UNUSED', gzcompress(serialize(new isPlainVar('Variable')), 9));

// endregion: with tools

if (!function_exists('toCollect')) {
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

if (!function_exists('toCollectWithModel')) {
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
        return is_collection($var) ? $var : collect($var);
    }
}

if (!function_exists('toCollectOrModel')) {
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

if (!function_exists('collectGet')) {
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

if (!function_exists('boolval')) {
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

if (!function_exists('toBoolValue')) {
    /**
     * Returns value as boolean
     *
     * @param $var
     *
     * @return bool
     */
    function toBoolValue($var): bool
    {
        if (is_bool($var)) return boolval($var);

        !is_bool($var) && ($var = strtolower(trim($var)));
        !is_bool($var) && ($var = $var === 'false' ? false : $var);
        !is_bool($var) && ($var = $var === 'true' ? true : $var);
        !is_bool($var) && ($var = $var === '1' ? true : $var);
        !is_bool($var) && ($var = $var === '0' ? false : $var);

        return boolval($var);
    }
}

if (!function_exists('toVar')) {
    /**
     * Returns value as boolean
     *
     * @param $var
     *
     * @return bool
     */
    function toVar($value = null, callable $callable = null): Closure
    {
        if ($callable && is_callable($callable)) {
            return function () use (&$callable, &$value) {
                return $callable->call(new class ($value) {
                    public $var = null;

                    public function __construct(&$var = null)
                    {
                        $this->var = &$var;
                    }

                    public function __toString()
                    {
                        return (string)$this->var;
                    }
                }, ...func_get_args());
            };
        } else {
            return function () use (&$value) {
                return $value;
            };
        }
    }
}

if (!function_exists('is_collection')) {
    function is_collection(&$var): bool
    {
        return $var instanceof \Illuminate\Support\Collection;
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


if (!function_exists('isLoggedIn')) {
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

if (!function_exists('ViewMode')) {
    /**
     * get current route
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function ViewMode()
    {
        try {
            return @end(explode('.', CurrentRoute()->getName()));
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('isViewMode')) {
    /**
     * get current route
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function isViewMode($mode)
    {
        return strtolower(trim($mode)) == strtolower(trim(ViewMode()));
    }
}

if (!function_exists('appDispatch')) {
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

if (!function_exists('currentMethod')) {
    /**
     * @param null $method
     * @return string
     */
    function currentMethod($method = null)
    {
        $method = $method ?: currentActionName();
        return (string)$method;
    }
}

if (!function_exists('currentModelName')) {
    /**
     * @param null $method
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


/**
 *
 */
!defined( 'DEBUG_METHODS') &&
define('DEBUG_METHODS', [
    'collectTrace',
    'traceInfo',
    'dumpDebug',
    'getDumpOutput',
    'dump',
    'du',
    'dx',
    'd',
    'dd',
    'dE',
]);

#region DEBUG
if (!function_exists('collectTrace')) {
    /**
     * @param null $file
     * @param null $line
     * @param null $object
     * @param null $method
     * @param null $string
     * @param null $debugTrace
     *
     * @return array
     */
    function collectTrace(
        &$file = null,
        &$line = null,

        &$object = null,
        &$method = null,

        &$string = null,
        &$debugTrace = null
    )
    {
        try {
            $call = [];
            if (!isDebugEnabled()) {
                {
                    return compact( 'line', 'file', 'call', 'string', 'debugTrace' );
                }
            }

            $debugTrace = $debugTrace ?: @debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            if (!empty($debugTrace) AND is_array($debugTrace)) {
                $idx = -1;
                $searchin = array_flip(DEBUG_METHODS);
                foreach ($debugTrace as $key => $item) {
                    $functionName = array_get($item, 'function', false);
                    if ($functionName && Arr::has(
                            $searchin,
                            $functionName
                        )) {
                        $idx = $key;
                    }
                }

                @reset($debugTrace);
                if ( (int) $idx > -1) {
                    try {
                        $calls = array_splice( $debugTrace, (int) $idx + 0, 2);
                        $call = array_merge(
                            $fst = array_get($calls, '1'),
                            $scnd = array_only(array_get($calls, '0'), ['file', 'line'])
                        );
                    } catch (Exception $exception) {
                        $call = @current($debugTrace);
                    }
                } else {
                    $call = @current( $debugTrace );
                }
            } else {
                throw new Exception( __LINE__ . " function debug_backtrace() returned: {$debugTrace}" );
            }

            $line = ( $call['line'] ?? __LINE__ );
            $file = @basenameOf(( $call['file'] ?? cutBasePath() ));
            $method = ( $call['function'] ?? __METHOD__ );
            $object = ( $call['class'] ?? __CLASS__ );
            $parentRelatoinType = ( $call['type'] ?? '::' );

            $classWithMethod = iif($object, isConsole( (string) ( $object ), "<b style='color: #6c1512'>{$object}</b>")) .
                iif($object && $method, getAny($parentRelatoinType, '::'), '') .
                iif($method,
                    isConsole( (string) ( $method ), "<b style='color: #0b6c0e'>{$method}</b>")
                );
            $fileWithLine = $file .
                iif($file && $line,
                    isConsole( ':', '<b>:</b>' ),
                    ''
                ) .
                iif(
                    $line,
                    isConsole( (string) ( $line ), "<b>{$line}</b>")
                );
            $string =
                iif($classWithMethod,
                    isConsole( (string) ( $classWithMethod ), "<b style='color: #0e566c'>{$classWithMethod}</b>")
                ) .
                iif(
                    $fileWithLine,
                    isConsole("    | {$fileWithLine}", "    <b>|</b><small style='color: blue;'>{$fileWithLine}</small>")
                );

        } catch (Exception $e) {
            d(
                __FILE__ . ':' . __LINE__,
                $e->getMessage(),
                collect(debug_backtrace())
            );
        }

        return compact(
            'line',
            'file',
            'call',
            'string',
            'debugTrace'
        );
    }
}

if (!function_exists('traceInfo')) {
    /**
     * @param null $debugBacktrace
     *
     * @return array
     */
    function traceInfo(&$debugBacktrace = null)
    {
        $file = null;
        $line = null;
        $method = null;
        $debugBacktrace = $debug_backtrace ?? @debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $string = null;

        return collectTrace( $file, $line, $class, $method, $string, $debugBacktrace );
    }
}

if (!function_exists('dumpDebug')) {
    /**
     * @param       $debug
     * @param mixed ...$args
     */
    function dumpDebug($debug, ...$args)
    {
        try {
            $isRunningConsole = isConsole( 'Yes', 'No' );

            $debug = $file = $line = $method = null;
            /** @var string|null $string */
            $string = null;
            $debug = @debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            extract(traceInfo($debug), null);
//dd(traceInfo($debug));
            if (isConsole()) {
                consoleBox($string);
            } else {
                echo $string;
            }

            foreach ($args as $v) {
                VarDumper::dump($v);
            }
        } catch (Exception $e) {
            $msg = isConsole(
                '' . cutBasePath() . ':' . __LINE__ . '.' . PHP_EOL . '' . __CLASS__ . '@' . __METHOD__ . '.' . PHP_EOL,

                "<b class='sf-dump-str'><small>" . cutBasePath() . '</small></b>' . '<span >:</span>' .
                "<b class='sf-dump-meta'><small>" . __LINE__ . '</small></b>' . '<span >@</span>' .
                "<b class='sf-dump-num'><small>" . __METHOD__ . '</small></b>' .
                "<hr style='font-size: 0.5px;'>"
            );

            $file = toCollect(explode(DIRECTORY_SEPARATOR, $e->getFile()));
            $allError = collect($e->getTrace())->splice(1, 5);
            $firstErr = $allError->shift();
            $_data = '';

            toCollect([$firstErr])
                ->each( static function ($_v) use (&$_data, $file) {
                    foreach ($_v as $k => $v) {
                        if (isArrayableOrArray($v)) {
                            return;
                        }

                        if ($k === 'args') {
                            $_data .= getDumpOutput($v);

                        } else if ($k === 'line') {
                            $_data .= makeCol('line', [ 2,
                                    ' ', '#']) .
                                isConsole($v, "<span class='sf-dump-num'>{$v}</span>");

                        } else if ($k === 'file') {
                            $_file = $file->take(-2)->implode(DIRECTORY_SEPARATOR);
                            $_path = $file->splice(0, $file->count() - 2)->implode(DIRECTORY_SEPARATOR);

                            $_data .= makeCol('file', [ 2,
                                    ' ', '#']) .
                                isConsole("{$_path}/{$_file}",
                                    '"' . "<span class='sf-dump-str'>" .
                                    "<span class='sf-dump-ellipsis sf-dump-ellipsis-path'>{$_path}</span>" .
                                    "<span class='sf-dump-ellipsis'>/</span>" .
                                    $_file . '</span>' .
                                    '"'
                                );

                        } else {
                            $_data .= makeCol($k, [ 2,
                                    ' ',
                                    '#'
                                ]) .
                                isConsole($v,
                                    '"' .
                                    "<span class='sf-dump-str'>{$v}</span>" .
                                    '"'
                                );
                        }

                        $_data .= isConsole( "\n", '<br>' );
                    }
                });

            $msg .= isConsole( '   Error:' . PHP_EOL . $_data . PHP_EOL,
                "<pre class='sf-dump'>" .
                "<span class='sf-dump-note'>Error:</span> {<br>" .
                "<samp data-depth='1' class='sf-dump-expanded'>{$_data}</samp>" . '} </pre>'
            );
            echo $msg;
            VarDumper::dump($e);
        }

        $lasDebugger = array_get(@debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1, []);

        $method = array_get($lasDebugger, 'function', '');
        $line = array_get($lasDebugger, 'line', '');
        $file = array_get($lasDebugger, 'file', '');
        $class = array_get($lasDebugger, 'class', '');
        $type = array_get($lasDebugger, 'type', '@');
        $lastDebug = isConsole( [
            'Method: ' . iif( $method, $method, '[ No Method ]' ) . '.',
            'File: ' . when( $file, function () use($file) { return cutBasePath($file); }, '[ No File ]' ) . iif( $file && $line, "@{$line}", getAny( $line, '[ No Line ]' )) . '.',
            'Class: ' . getAny( $class, '[ No Class ]' ) . '.',
            "Running In Console: [ {$isRunningConsole} ].",
        ],
            '<small>By: <b>' . cutBasePath( $file) . ':' . $line . '</b>;  ' . $class . $type . '<b>' . $method . '</b></small> <br>'
        );

        if ($lastDebug && is_array($lastDebug) && App::runningInConsole()) {
            consoleBox( $lastDebug, STR_PAD_RIGHT, 'End Of Debug' );
        } else if ($lastDebug && !App::runningInConsole()) {
            echo($lastDebug);
        }
    }
}

/**
 * dump to memory & return the result
 */
if (!function_exists('getDumpOutput')) {
    /**
     * @return string
     */
    function getDumpOutput()
    {
        if (!isDebugEnabled()) {
            return "";
        }

        $_data = '';
        ob_start();
        $d = toCollect(func_get_args());
        $d->dump();

        $_data = ob_get_contents();
        ob_end_clean();

        return $_data;
    }
}

if (!function_exists('dump')) {
    /**
     * @param mixed   $var
     * @param mixed[] ...$moreVars
     *
     * @return array|mixed $var
     * @author Nicolas Grekas <p@tchwork.com>
     */
    function dump($var, ...$moreVars)
    {
        if (!isDebugEnabled()) {
            return $var;
        }

        VarDumper::dump($var);

        foreach ($moreVars as $v) {
            VarDumper::dump($v);
        }

        if (1 < func_num_args()) {
            return func_get_args();
        }

        return $var;
    }
}

if (!function_exists('dd')) {
    /**
     * @param mixed ...$vars
     */
    function dd(...$vars)
    {
        if (!isDebugEnabled()) {
            return;
        }

        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        die(1);
    }
}

if (!function_exists('du')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed $args
     *
     * @return void
     */
    function du(...$args)
    {
        dumpDebug(@debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), ...$args);
    }
}

if (!function_exists('d')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed $args
     *
     * @return void
     */
    function d(...$args)
    {
        if (!isDebugEnabled()) {
            return;
        }

        dumpDebug(@debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), ...$args);

        die(1);
    }
}

if (!function_exists('dx')) {
    /**
     * Empty Function.
     *
     * @param  mixed $args
     *
     * @return void
     */
    function dx(...$args) { }
}
#endregion


/**
 * Shortcut: get_class_methods
 */
if (!function_exists('_gcm')) {
    /**
     * @return array
     */
    function _gcm()
    {
        return get_class_methods(...func_get_args());
    }
}

/**
 * Shortcut: get_class
 */
if (!function_exists('_gc')) {
    /**
     * @return string
     */
    function _gc()
    {
        return get_class(...func_get_args());
    }
}

/**
 * Shortcut: class_exists
 */
if (!function_exists('_ce')) {
    /**
     * @return bool
     */
    function _ce()
    {
        return class_exists(...func_get_args());
    }
}

// region: console

if (!function_exists( 'isDebugEnabled')) {
    /**
     * @return bool
     */
    function isDebugEnabled()
    {
        try {
            $request = request();
            if (($request->is('api/*') || $request->expectsJson()) && session('disable_d') !== false) {
                return false;
            }

            $fromWeb = !app('request')->headers->has('app-name') && !App::runningInConsole();
            $fromConsole = !app('request')->headers->has('app-name') && App::runningInConsole();
            $fromApi = app('request')->headers->has('app-name') && !App::runningInConsole();

            if ($fromConsole === true || session('disable_d') === false) {
                return true;
            } else if ($fromApi === true) {
                return false;
            } else if ($fromWeb === true) {
                return false;
            }

            return
                !app('request')->headers->has('app-name') && !App::runningInConsole();

        } catch (Exception $exception) {
            return false;
        }
    }
}

if (!function_exists('isDebugDisabled')) {
    /**
     * @return bool
     */
    function isDebugDisabled()
    {
        return !isDebugEnabled();
    }
}

if (!function_exists('debugEnable')) {
    /**
     * @param \Illuminate\Session\SessionManager|null $session
     * @param bool                                    $status
     */
    function debugEnable(\Illuminate\Session\SessionManager $session = null, $status = true)
    {
        $session = $session ?: session();
        $session->put('disable_d', !$status);
        $session->save();

    }
}

if (!function_exists('dE')) {
    /**
     *
     */
    function dE()
    {
        debugEnable();
        d(...func_get_args());
    }
}

if (!function_exists('duE')) {
    /**
     *
     */
    function duE()
    {
        debugEnable();
        du(...func_get_args());
    }
}


if (!function_exists('makeCol')) {
    /**
     * @param        $text
     * @param array  $intend
     * @param string $elm
     * @param string $class
     * @param array  $replacer
     *
     * @return string
     */
    function makeCol(
        $text,
        $intend = [0, ' ', '#'],
        $elm = 'span',
        $class = 'sf-dump-protected',
        $replacer = ['~', ':']
//                    ,$length = 5
    )
    {
        return str_repeat($intend[1], $intend[0]) .
            $intend[2] .
            isConsole( '', "<{$elm} class='{$class}'>") .

            str_ireplace($replacer[0],
                isConsole($replacer[1], "</{$elm}>{$replacer[1]}<{$elm} class='{$class}'>"),
                "{$text}{$replacer[0]}") .
            isConsole( ' ', "</{$elm}> ");
    }
}

/**
 * Drow box with text inside it
 */
if (!function_exists('consoleBox')) {
    /**
     * @param string $msgs
     * @param int    $align
     * @param string $title
     */
    function consoleBox($msgs = '', $align = STR_PAD_RIGHT, $title = 'Debug')
    {
        if (!isDebugEnabled()) {
            return;
        }

        $text_length = 50;
        $defaults = valueToObject($default = [
            'align' => STR_PAD_BOTH,
            'title' => 'Debug',
            'border' => [
                'left' => '|',
                'center' => '-',
                'right' => '|',
            ],
        ]);

        $title = is_null($title) ? $defaults->title : $title;
        $align = is_null($align) ? $default['align'] : $align;


        echo PHP_EOL .
            isConsole(
                $default['border']['left'] .
                prefixText(" {$title} ", $default['border']['center'], $text_length, STR_PAD_BOTH)
                . $default['border']['right']
            ) . PHP_EOL;

        toCollect($msgs)->each( static function ($msg) use ($align, $text_length, $default) {
            echo isConsole(
                $default['border']['left'] .
                prefixText( $msg, ' ', $text_length, $align) .
                $default['border']['right'] . PHP_EOL
            );
        });

        echo isConsole(
                $default['border']['left'] .
                prefixText( '', $default['border']['center'], $text_length, STR_PAD_BOTH) .
                $default['border']['right']
            ) . PHP_EOL;
    }
}


// endregion: console

// region: current

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

// endregion:

// region: files

if (!function_exists('unzip')) {
    /**
     * UnZip .zip archive.
     *
     * @param string $archivePath .zip path
     * @param string|null $extractToPath Destination directory path.
     *
     * @return bool
     */
    function unzip($archivePath, $extractToPath = null)
    {
        $path = $extractToPath ?: getcwd();
        $file = $archivePath;
        if (!file_exists($file)) return false;

        $zip = new ZipArchive();
        $res = $zip->open($file);
        if ($res === TRUE) {
            // extract it to the path we determined above
            $zip->extractTo($path);
            $zip->close();

            return true;
        }

        return false;
    }
}

if (!function_exists('includeAllSubFiles')) {
    /**
     * Include php files
     */
    function includeAllSubFiles($__DIR__, $__FILE__ = "", callable $incCallBack = null)//: \Illuminate\Support\Collection
    {
        $__DIR__ = rtrim($__DIR__, DIRECTORY_SEPARATOR) . str_start($__FILE__, DIRECTORY_SEPARATOR);

//        if (!is_callable($incCallBack)) {
//            $incCallBack = function($v) { return $v; };
//        }

        $mCojntetnt = function ($v) use ($incCallBack) {
            if ($v->getExtension() != 'php') return false;

            if ($incCallBack && is_callable($incCallBack)) {
                return $incCallBack($v->getPathname());
            }

            return include($v->getPathname());

        };

        $__DIR__ = fixPath($__DIR__);
        if (file_exists($__DIR__)) {
            return collect((new Filesystem)->allFiles($__DIR__))
                ->map($mCojntetnt);
        } else {
            dE(
                "Path [{$__DIR__}] not exists!"
            );
        }

        return null;
    }
}

if (!function_exists('includeIfExists')) {
    /**
     * Include file if exist
     */
    function includeIfExists($file)
    {
        return file_exists($file) ? include($file) : false;
    }
}

if (!function_exists('fixPath')) {
    /**
     * Fix slashes/back-slashes replace it with DIRECTORY_SEPARATOR.
     *
     * @param string $path
     *
     * @return string
     */
    function fixPath(string $path) {
        return replaceAll([ "\\" => DIRECTORY_SEPARATOR ], $path);
    }
}

if (!function_exists('includeMenuPartials')) {
    /**
     * Include menu files
     *
     * @param string $partialsDir
     * @param string $partialsFile
     * @param null|array $mergeWith
     * @param string $partialsDirName
     *
     * @return array
     */
    function includeMenuPartials($partialsDir, $partialsFile, $mergeWith = null, $partialsDirName = "partials")
    {
        $partialsDirName = trim($partialsDirName, "\\");

        $menus = toCollect(includeAllSubFiles(
            $partialsDir . "\\{$partialsDirName}\\",
            str_before(basenameOf($partialsFile), ".php"),
            fn($file)=>includeIfExists($file)
        ));
        $menu = collect();
        $menus->each(function ($v) use(&$menu) {
            $menu = $menu->mergeRecursive($v);
        });

        if(!is_null($mergeWith)) {
            $menu = $menu->mergeRecursive($mergeWith);
        }

//if($menu->isEmpty()) {
//    dump([
//        $partialsDir . "\\{$partialsDirName}\\",
//        str_before(basenameOf($partialsFile), ".php"),
//        $menu->all()
//    ]);
//}
        return $menu->all();
    }
}
// endregion: files

// region:general

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
 * return table name}
 */
if (!function_exists('getTable')) {
    /**
     * Returns Model table name.
     *
     * @param string $model Model class.
     *
     * @return null|string
     */
    function getTable(string $model)
    {
        if ($model && class_exists($model)) {
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
if (!function_exists('getMethods')) {
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
if (!function_exists('getFillable')) {
    /**
     * Returns Model Fillable.
     *
     * @param string $model Model class.
     *
     * @return null|array
     */
    function getFillable(string $model)
    {
        if ($model && class_exists($model)) {
            $class = new $model;
            /** @var $class \Illuminate\Database\Eloquent\Model */
            return $class->getFillable();
        }

        return null;
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


// endregion: general

// region: getters

if (!function_exists('getTrans')) {
    /**
     * Returns Translation or return default.
     *
     * @param string|null $lang_path lang path
     * @param null|mixed $default default value to return if trans not exists
     *
     * @return mixed
     */
    function getTrans($lang_path, $default = null)
    {
        $trans = ($trans = __($lang_path)) != $lang_path ? $trans : $default;

        return $trans;
    }
}

if (!function_exists('cutBasePath')) {
    /**
     * Remove base_path() from the given file path.
     *
     * @param string $fullFilePath file path
     * @param string $prefix any text to prefix the result with.
     *
     * @return string
     */
    function cutBasePath($fullFilePath = null, $prefix = '')
    {
        $fullFilePath = $fullFilePath ?:
            Arr::get(@current(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)), 'file', null);

        return $prefix . str_ireplace(base_path() . DIRECTORY_SEPARATOR, '', $fullFilePath ?: __FILE__);
    }
}

if (!function_exists('classPropertyValue')) {
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

if (!function_exists("filesMap")) {
    /**
     * Get Files names into collect()->mapWithKeys()->filter()->toArray() list as [ FilenameWithoutExtension => $callabke(RealPath) ]
     *
     * @param $path
     * @param callable|null $callback
     * @param null $default
     * @return null
     */
    function filesMap($path, callable $callback = null, $default = null)
    {
        $data = null;
        $path = (new Filesystem)->exists($path) ? $path : null;

        if ($path) {
            $data = collect((new Filesystem())->files(...func_get_args()))->mapWithKeys(function ($v) use ($callback) {
                /** @var $v \Symfony\Component\Finder\SplFileInfo */
                $map = [pathinfo($f->getFilename(), PATHINFO_FILENAME) => $v->getRealPath()];
                return is_callable($callback) ? $callback($map) : $map;
            })->filter()->toArray();
        }

        return $data ?: ($default ?: null);
    }
}

if (!function_exists("getByKey")) {
    /**
     * @param $data
     * @param null $key
     * @return array|mixed|null
     */
    function getByKey($data, $key = null)
    {
        iF(is_null($key)) {
            return $data;
        }
        $data = valueToArray($data ?: []);

        if($key && array_has($data, $key)) {
            $data = array_get($data, $key, []);
        }

        return $data;
    }
}

if (! function_exists('getOld')) {
    /**
     * Retrieve an old input item.
     *
     * @param string|null $key
     * @param Model|null $model
     * @param mixed $default
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
// endregion: getters



// region: is

/**
 * return bool
 */
if (!function_exists('isArrayable')) {
    /**
     * Check if the given var is Arrayable (has ->toArray()).
     *
     * @param mixed|null $array
     *
     * @return bool
     */
    function isArrayable($array): bool
    {
        return $array instanceof \Illuminate\Contracts\Support\Arrayable || method_exists($array, 'toArray');
    }
}

/**
 * return bool
 */
if (!function_exists('isArrayableOrArray')) {
    /**
     * Check if the given var is Array | is Arrayable (has ->toArray()).
     *
     * @param mixed|null $array
     *
     * @return bool
     */
    function isArrayableOrArray($array): bool
    {
        return is_array($array) || isArrayable($array);
    }
}

/**
 * return bool
 */
if (!function_exists('isAllable')) {
    /**
     * Check if the given var is Allable (has ->all()).
     *
     * @param array|\Illuminate\Contracts\Support\Arrayable|\Illuminate\Support\Collection|mixed $array
     *
     * @return bool
     */
    function isAllable($array): bool
    {
        return method_exists($array, 'all');
    }
}

/**
 * return bool
 */
if (!function_exists('isPaginator')) {
    /**
     * Check if the given var is paginator instance.
     *
     * @param $value
     *
     * @return bool
     */
    function isPaginator($value): bool
    {
        return (
            $value instanceof \Illuminate\Pagination\LengthAwarePaginator ||
            $value instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ||
            $value instanceof \Illuminate\Pagination\Paginator ||
            $value instanceof \Illuminate\Contracts\Pagination\Paginator ||
            $value instanceof \Illuminate\Pagination\AbstractPaginator ||

            (class_exists($class = "League\\Fractal\\Pagination\\PaginatorInterface") && $value instanceof $class) ||
            (class_exists($class = "League\\Fractal\\Pagination\\PaginatorInterface") && $value instanceof $class) ||
            (class_exists($class = "League\\Fractal\\Pagination\\IlluminatePaginatorAdapter") && $value instanceof $class) ||
            (class_exists($class = "League\\Fractal\\Pagination\\PagerfantaPaginatorAdapter") && $value instanceof $class) ||
            (class_exists($class = "League\\Fractal\\Pagination\\DoctrinePaginatorAdapter") && $value instanceof $class)
        );
    }
}

/**
 * return bool
 */
if (!function_exists('isPaginated')) {
    /**
     * Check if the given var is paginate result.
     *
     * @param $value
     *
     * @return null
     */
    function isPaginated($value)
    {
        if (isPaginator($value) || method_exists($value, 'getCollection')) {
            return true;
        }

        if (is_array($value)) {
            if (
                Arr::has($value, ['current_page', 'per_page']) ||
                Arr::has($value, 'meta')
            ) {
                return true;
            }
        }

        return false;
    }
}

/**
 * return mixed
 */
if (!function_exists('isConsole')) {
    /**
     *
     * ### Check if the application running in `Console (CLI)`.
     * *Return custom response by checking __App::runningInConsole()__ method.*
     *
     * ---
     * --|| **Basically the return is one of two variables.**
     *
     * -----| **$runningInConsole** By default its `true`, Returns this **ONLY** If App. is in **Console**.
     *
     * -----| **$notRunningInConsole** By default its `false`, Returns this **ONLY** If App. is **NOT** in **Console**.
     *
     * @param mixed $runningInConsole | return value of ( $runningInConsole ) when App is running in console.
     * @param mixed $notRunningInConsole | return value of ( $notRunningInConsole ) when App is NOT running in console.
     *
     * @return mixed
     */
    function isConsole($runningInConsole = true, $notRunningInConsole = false)
    {
        return App::runningInConsole() ? $runningInConsole : $notRunningInConsole;
    }
}

/**
 * return bool
 */
if (!function_exists('isBuilder')) {
    /**
     * ### Check if the given var is Query Builder | Eloquent Builder | Relation.
     *
     * @param \Illuminate\Database\Query\Builder|Builder|Relation|mixed $var | return $var === QueryBuilder.
     *
     * @return bool
     */
    function isBuilder($var): bool
    {
        return $var instanceof \Illuminate\Database\Query\Builder || $var instanceof Builder || $var instanceof Relation;
    }
}

/**
 * return bool
 */
if (!function_exists('isLoggedIn')) {
    /**
     * ### Check if user has logged in.
     *
     * @return bool
     */
    function isLoggedIn(): bool
    {
        return !!Auth()->check();
    }
}

/**
 * return bool
 */
if (!function_exists('isGuest')) {
    /**
     * ### Check if user is guest.
     *
     * @return bool
     */
    function isGuest(): bool
    {
        return !!Auth()->guest();
    }
}

if (!function_exists('endsWithAny')) {
    /**
     * Determine if a given string ends with a given substrings then return substring or False when fail.
     *
     * @param string $haystack
     * @param string|array $needles
     * @return string
     */
    function endsWithAny($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (Str::endsWith($haystack, $needle))
                return $needle;
        }

        return false;
    }
}

if (!function_exists('isInstanceOf')) {
    /**
     * Determine if a given object is an instance of second object
     *
     * @param $object
     * @param $ofThat
     *
     * @return bool
     */
    function isInstanceOf($object, $ofThat)
    {
        $noClass = function ($o) {
            return is_object($o) ? get_class($o) : $o;
        };

        try {
            $_ofThat = $noClass($ofThat);

            return ($object instanceof $ofThat) ||
                ($object instanceof $_ofThat) ||
                is_a($object, $ofThat) ||
                is_a($object, $_ofThat);

        } catch (Exception $exception) {

        }

        return false;
    }
}

if (!function_exists('isModel')) {
    /**
     * Determine if a given object is inherit Model class.
     *
     * @param object $object
     *
     * @return bool
     */
    function isModel($object)
    {
        try {
            return ($object instanceof Model) || is_a($object, Model::class);
        } catch (Exception $exception) {

        }

        return false;
    }
}

if (!function_exists('getModelKey')) {
    /**
     * Returns Model Key Only!
     *
     * @param $object
     *
     * @return mixed|object|int
     */
    function getModelKey($object)
    {
        if(isModel($object)) {
            $key = $object->getKeyName() ?: 'id';
            return $object->getKey() ?: $object->{$key} ?: (
            object_get($object, $key) ?:
                array_get($object->toArray(), $key) ?: null
            );
        }

        return $object;
    }
}

if (!function_exists('array_keys_exists')) {
    /**
     * Easily check if multiple array keys exist.
     *
     * @param array $keys
     * @param array $arr
     *
     * @return boolean
     */
    function array_keys_exists(array $keys, array $arr)
    {
        return !array_diff_key(array_flip($keys), $arr);
    }
}

// endregion: is

// region: Tools

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
// endregion: Tools

// region: Value

/**
 * return string
 */
if (!function_exists('valueToDate')) {
    /**
     * Returns value as date format
     *
     * @param $value
     *
     * @return null
     */
    function valueToDate($value)
    {
        return $value ? carbon()->parse($value)->toDateString() : null;
    }
}

/**
 * return string
 */
if (!function_exists('valueToDateTime')) {
    /**
     * Returns value as date and time format
     *
     * @param $value
     *
     * @return null
     */
    function valueToDateTime($value)
    {
        return $value ? carbon()->parse($value)->toDateTimeString() : null;
    }
}

/**
 * return array
 */
if (!function_exists('valueToArray')) {
    /**
     * Returns value as Array
     *
     * @param $value
     *
     * @param bool $forceToArray
     * @return null|array
     */
    function valueToArray($value, bool $forceToArray = false)
    {
        if ($value instanceof Traversable) {
            return iterator_to_array( $value );
        }
        $collect = toCollect($value);

        return $forceToArray ? $collect->toArray() : (is_array($collectAll = $collect->all()) ? $collectAll : $collect->toArray());
    }
}

/**
 * return array
 */
if (!function_exists('valueToDotArray')) {
    /**
     * Returns value as Array
     *
     * @param $value
     *
     * @return null|array
     */
    function valueToDotArray($value)
    {
        $array = [];

        collect($value)->mapWithKeys(function ($value, $key) use(&$array) {
            return array_set($array, $key, $value);
        });

        return $array;
    }
}

/**
 * return object
 */
if (!function_exists('valueToObject')) {
    /**
     * Returns value as Object
     *
     * @param $value
     *
     * @return null|object
     */
    function valueToObject($value)
    {
        return (object)$value;
    }
}

// endregion: Value


// region:
// endregion:


