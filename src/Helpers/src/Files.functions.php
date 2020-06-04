<?php

use Illuminate\Filesystem\Filesystem;

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

if (!function_exists('filenameWithoutExtension')) {
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