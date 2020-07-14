<?php

use Illuminate\Filesystem\Filesystem;

if ( !defined('DIRECTORY_SEPARATOR_L') ) {
    /**
     * opposite of DIRECTORY_SEPARATOR
     */
    @define("DIRECTORY_SEPARATOR_L", "/");
}

if ( !function_exists('unzip') ) {
    /**
     * UnZip .zip archive.
     *
     * @param string      $archivePath .zip path
     * @param string|null $extractToPath Destination directory path.
     *
     * @return bool
     */
    function unzip($archivePath, $extractToPath = null)
    {
        $path = $extractToPath ?: getcwd();
        $file = $archivePath;
        if ( !file_exists($file) ) return false;

        $zip = new ZipArchive();
        $res = $zip->open($file);
        if ( $res === TRUE ) {
            // extract it to the path we determined above
            $zip->extractTo($path);
            $zip->close();

            return true;
        }

        return false;
    }
}


if ( !function_exists('includeSubFiles') ) {
    /**
     * Include php files
     */
    function includeSubFiles($__DIR__, $__FILE__ = null, callable $incCallBack = null): void
    {
        $__FILE__ = $__FILE__ ? rtrim(basename($__FILE__), '.php') : "";
        $__DIR__ = $__DIR__ ? rtrim($__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : "";
        $sub_path = $__DIR__ . /*($__DIR__ && $__FILE__ ? DIRECTORY_SEPARATOR : "") .*/
            $__FILE__;

        if ( file_exists($sub_path) ) {
            collect(File::files($sub_path))->map(function ($v) use ($incCallBack) {
                if ( $v->getExtension() != 'php' ) return false;

                if ( $incCallBack && is_callable($incCallBack) ) {
                    $incCallBack($v->getPathname());
                } else {
                    include_once $v->getPathname();
                }
            });
        }
    }
}

if ( !function_exists('includeAllSubFiles') ) {
    /**
     * Include php files
     */
    function includeAllSubFiles($__DIR__, $__FILE__ = "", callable $incCallBack = null): void
    {
        $__DIR__ = rtrim($__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $__FILE__;

        if ( file_exists($__DIR__) ) {
            collect(File::allFiles($__DIR__))->map(function ($v) use ($incCallBack) {
                if ( $v->getExtension() != 'php' ) return false;

                if ( $incCallBack && is_callable($incCallBack) ) {
                    $incCallBack($v->getPathname());
                } else {
                    include_once $v->getPathname();
                }

                return $v;
            });
        }
    }
}

if ( !function_exists('includeIfExists') ) {
    /**
     * Include file if exist
     */
    function includeIfExists($file)
    {
        return file_exists($file) ? include($file) : false;
    }
}

if ( !function_exists('fixPath') ) {
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

if ( !function_exists('includeMenuPartials') ) {
    /**
     * Include menu files
     *
     * @param string     $partialsDir
     * @param string     $partialsFile
     * @param null|array $mergeWith
     * @param string     $partialsDirName
     *
     * @return array
     */
    function includeMenuPartials($partialsDir, $partialsFile, $mergeWith = null, $partialsDirName = "partials")
    {
        $partialsDirName = trim($partialsDirName, "\\");

        $menus = toCollect(includeAllSubFiles(
            $partialsDir . "\\{$partialsDirName}\\",
            str_before(basenameOf($partialsFile), ".php"),
            fn($file) => includeIfExists($file)
        ));
        $menu = collect();
        $menus->each(function ($v) use (&$menu) {
            $menu = $menu->mergeRecursive($v);
        });

        if ( !is_null($mergeWith) ) {
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

if ( !function_exists('filenameWithoutExtension') ) {
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
