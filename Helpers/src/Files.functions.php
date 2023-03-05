<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Filesystem\Filesystem;

if( !defined('DIRECTORY_SEPARATOR_L') ) {
    /**
     * opposite of DIRECTORY_SEPARATOR
     */
    @define("DIRECTORY_SEPARATOR_L", "/");
}

if( !function_exists('unzip') ) {
    /**
     * UnZip .zip archive.
     *
     * @param string      $archivePath   zip path
     * @param string|null $extractToPath Destination directory path.
     *
     * @return bool
     */
    function unzip($archivePath, $extractToPath = null)
    {
        $path = $extractToPath ?: getcwd();
        $file = $archivePath;
        if( !file_exists($file) ) return false;

        $zip = new ZipArchive();
        $res = $zip->open($file);
        if( $res === TRUE ) {
            // extract it to the path we determined above
            $zip->extractTo($path);
            $zip->close();

            return true;
        }

        return false;
    }
}

if( !function_exists('includeSubFiles') ) {
    /**
     * Include php files
     */
    function includeSubFiles($__DIR__, $__FILE__ = null, callable $incCallBack = null): void
    {
        $__FILE__ = $__FILE__ ? rtrim(basename($__FILE__), '.php') : "";
        $__DIR__ = $__DIR__ ? rtrim($__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : "";
        $sub_path = $__DIR__ . /*($__DIR__ && $__FILE__ ? DIRECTORY_SEPARATOR : "") .*/
            $__FILE__;

        if( file_exists($sub_path) ) {
            collect((new Filesystem)->files($sub_path))
                ->map(function($v) use ($incCallBack) {
                    if( trimLower($v->getExtension()) !== 'php' ) {
                        return false;
                    }

                    if( $incCallBack && is_callable($incCallBack) ) {
                        $incCallBack($v->getPathname());
                    }

                    include_once $v->getPathname();
                });
        }
    }
}

if( !function_exists('includeAllSubFiles') ) {
    /**
     * Include php files
     */
    function includeAllSubFiles($__DIR__, $__FILE__ = "", callable $incCallBack = null)
    {
        $__DIR__ = rtrim($__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $__FILE__;

        if( file_exists($__DIR__) ) {
            return collect((new Filesystem)->allFiles($__DIR__))
                ->map(function($v) use ($incCallBack) {
                    /** @var \Symfony\Component\Finder\SplFileInfo $v */
                    if( $v->getExtension() !== 'php' ) {
                        return false;
                    }

                    if( $incCallBack && is_callable($incCallBack) ) {
                        return $incCallBack($v->getPathname());
                    }

                    return includeIfExists($v->getPathname());
                    //                return $v;
                });
        }

        return collect();
    }
}

if( !function_exists('includeIfExists') ) {
    /**
     * Include file if exist
     *
     * @param string              $file
     * @param callable|mixed|null $when_not_exists
     *
     * @return null|mixed
     */
    function includeIfExists($file, $when_not_exists = null)
    {
        return file_exists($file) ? include($file) : getValue($when_not_exists);
    }
}

if( !function_exists('includeOnceIfExists') ) {
    /**
     * Include file Once if exist
     *
     * @param string              $file
     * @param callable|mixed|null $when_not_exists
     * @param callable|mixed|null $when_already_included
     *
     * @return bool|mixed
     */
    function includeOnceIfExists($file, $when_not_exists = null, $when_already_included = null)
    {
        if( file_exists($file) ) {
            if( ($return = include_once($file)) === true ) {
                $return = isClosure($when_already_included) ? getValue($when_already_included, ...[ $file ]) : $when_already_included;
            }
        } else {
            $return = $when_not_exists = isClosure($when_not_exists) ? getValue($when_not_exists, ...[ $file ]) : $when_not_exists;
        }

        return getValue($return, ...[ $file ]);
    }
}
/*
if ( !function_exists('includeIfExists') ) {
    /**
     * Include file if exist
     *
     * @param string $file
     * @param bool   $once
     *
     * @return false|mixed
     *//*
    function includeIfExists($file, $once = true)
    {
        $include = $once ? "include_once" : "include";
        return file_exists($file) && is_callable($include) ? $include($file) : false;
    }
}*/

if( !function_exists('fixPath') ) {
    /**
     * Fix slashes/back-slashes replace it with DIRECTORY_SEPARATOR.
     *
     * @param string $path
     *
     * @return string
     */
    function fixPath(string $path, $replace_separator_with = DIRECTORY_SEPARATOR)
    {
        $replace_separator_with = $replace_separator_with ?: DIRECTORY_SEPARATOR;

        return replaceAll([
                              "\\" => $replace_separator_with,
                              "/" => $replace_separator_with,
                              $replace_separator_with . $replace_separator_with => $replace_separator_with,
                          ], $path);
    }
}

if( !function_exists('includeMenus') ) {
    /**
     * Include menu files
     *
     * @param string     $menuDir
     * @param null|array $mergeWith
     * @param string     $partialsDirName
     *
     * @return array
     */
    function includeMenus($menuDir, $mergeWith = null, $partialsDirName = "menus")
    {
        $partialsDirName = trim($partialsDirName, "\\");

        $menu = collect();
        $menus = toCollect(
            includeAllSubFiles(
                $menuDir . "\\{$partialsDirName}\\",
                "",
                function($file) {
                    return includeOnceIfExists($file, [], []);
                }
            )
        );
//        dd(
//            $menus
//        );
//        $menus->each(function ($v) use (&$menu) {
//            $menu = $menu->mergeRecursive($v);
//        });

        $menu = $menus;
        if( !is_null($mergeWith) ) {
            $menu = $menu->mergeRecursive($mergeWith);
        }

//if($menu->isEmpty()) {
//    dump([
//        $menuDir . "\\{$partialsDirName}\\",
//        str_before(basenameOf($partialsFile), ".php"),
//        $menu->all()
//    ]);
//}
        return $menu->all();
    }
}

if( !function_exists('filenameWithoutExtension') ) {
    /**
     * returns the given filename with out extension
     *
     * @param string $filename
     *
     * @return string|string[]|null
     */
    function filenameWithoutExtension(string $filename)
    {
        return $filename ? pathinfo($filename, PATHINFO_FILENAME) : null;
    }
}
