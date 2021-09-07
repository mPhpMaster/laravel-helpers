<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if ( !function_exists('isDebugEnabled') ) {
    /**
     * @return bool
     */
    function isDebugEnabled()
    {
        try {
            $request = request();
            if ( ($request->is('api/*') || $request->expectsJson()) && session('disable_d') !== false ) {
                return false;
            }

            static $requestHasAppName = null;
            $runningInConsole = isRunningInConsole();

            $requestHasAppName = $requestHasAppName ?? app('request')->headers->has('app-name');

            $fromWeb = !$requestHasAppName && !$runningInConsole;
            $fromConsole = !$requestHasAppName && $runningInConsole;
            $fromApi = $requestHasAppName && !$runningInConsole;

            if ( $fromConsole === true || session('disable_d') === false ) {
                return true;
            }

            if ( $fromApi === true || $fromWeb === true ) {
                return false;
            }

            return $fromWeb;

        } catch (Exception $exception) {
            return false;
        }
    }
}

if ( !function_exists('isDebugDisabled') ) {
    /**
     * @return bool
     */
    function isDebugDisabled()
    {
        return !isDebugEnabled();
    }
}

if ( !function_exists('debugEnable') ) {
    /**
     * @param \Illuminate\Session\SessionManager|null $session
     * @param bool                                    $status
     */
    function debugEnable(\Illuminate\Session\SessionManager $session = null, $status = true)
    {
        $session ??= session();
        $session->put('disable_d', !$status);
        $session->save();

    }
}

if ( !function_exists('dE') ) {
    /**
     *
     */
    function dE(...$args)
    {
        debugEnable();

        dumpDebug(@getDebugBacktrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10), ...$args);

        die(1);
    }
}

if ( !function_exists('duE') ) {
    /**
     *
     */
    function duE(...$args)
    {
        debugEnable();
        dumpDebug(@getDebugBacktrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10), ...$args);
//        du(...func_get_args());
    }
}


if ( !function_exists('makeCol') ) {
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
            isConsole('', "<{$elm} class='{$class}'>") .

            str_ireplace($replacer[0],
                isConsole($replacer[1], "</{$elm}>{$replacer[1]}<{$elm} class='{$class}'>"),
                "{$text}{$replacer[0]}") .
            isConsole(' ', "</{$elm}> ");
    }
}

/**
 * Drow box with text inside it
 */
if ( !function_exists('consoleBox') ) {
    /**
     * @param string $msgs
     * @param int    $align
     * @param string $title
     */
    function consoleBox($msgs = '', $align = STR_PAD_RIGHT, $title = 'Debug')
    {
        if ( !isDebugEnabled() ) {
            return;
        }

        $text_length = 100;
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

        toCollect($msgs)->each(static function ($msg) use ($align, $text_length, $default) {
            $msg = " {$msg}";
            echo isConsole(
                $default['border']['left'] .
                prefixText($msg, ' ', $text_length, $align) .
                $default['border']['right'] . PHP_EOL
            );
        });

        echo isConsole(
                $default['border']['left'] .
                prefixText('', $default['border']['center'], $text_length, STR_PAD_BOTH) .
                $default['border']['right']
            ) . PHP_EOL;
    }
}

