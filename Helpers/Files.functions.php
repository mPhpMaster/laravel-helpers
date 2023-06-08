<?php
/*
 * Copyright © 2023. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

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
