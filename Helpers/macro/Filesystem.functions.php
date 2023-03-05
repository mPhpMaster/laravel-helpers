<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */


\Illuminate\Filesystem\Filesystem::macro(
    'extractZip',
    static function ($path, $extractTo) {
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === true) {
            $zip->extractTo($extractTo);
            $zip->close();
            return true;
        }

        return false;
    }
);
