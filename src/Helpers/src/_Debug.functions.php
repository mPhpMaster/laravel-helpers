<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Arr;
use Symfony\Component\VarDumper\VarDumper;

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
//                toCollect(data_get($debugTrace, '*.function'))->take(count(DEBUG_METHODS) + 2)->map->function->search(function ($fn) use(&$idxs){
//                    return \Illuminate\Support\Arr::has(DEBUG_METHODS, $fn);
//                });
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

//                    for ($counter = 0; $counter < $idx; $counter++)
//                        $call = @next($debugTrace);
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
            extract(traceInfo($debug));
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
