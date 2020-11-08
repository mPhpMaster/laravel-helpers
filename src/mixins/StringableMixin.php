<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace mPhpMaster\Support\mixins;

use Illuminate\Support\Stringable;

/**
 * Class StringableMixin
 *
 * @mixin \Illuminate\Support\Stringable
 * @uses  \Illuminate\Support\Str
 *
 * @package mPhpMaster\Support\mixins
 */
class StringableMixin
{
    /**
     * @return \Closure
     */
    public function toHumanReadableSize()
    {
        return function ($sizeInBytes = null) {
            /* @var $this \Illuminate\Support\Stringable */
            $sizeInBytes = is_null($sizeInBytes) ? $this->value : $sizeInBytes;
            $sizeInBytes = (int)$sizeInBytes instanceof Stringable ? "{$sizeInBytes}" : $sizeInBytes;
            $sizeInBytes = $sizeInBytes ?: 0;

            return $this->humanReadableSize($sizeInBytes)->__toString();
        };
    }

    /**
     * @return \Closure
     */
    public function humanReadableSize()
    {
        return function ($sizeInBytes = null) {
            $sizeInBytes = is_null($sizeInBytes) ? $this->value : $sizeInBytes;
            $sizeInBytes = (int)$sizeInBytes instanceof Stringable ? "{$sizeInBytes}" : $sizeInBytes;
            $sizeInBytes = $sizeInBytes ?: 0;

            $units = [
                getTrans('Byte', 'B'),
                getTrans('KiloBytes', 'KB'),
                getTrans('MegaByte', 'MB'),
                getTrans('GigaByte', 'GB'),
                getTrans('TeraByte', 'TB')
            ];

            if ( $sizeInBytes == 0 ) {
                return '0 ' . $units[1];
            }

            for ($i = 0; $sizeInBytes > 1024; $i++) {
                $sizeInBytes /= 1024;
            }

            $sizeInBytes = round($sizeInBytes, 2) . ' ' . $units[ $i ];

            return new static($sizeInBytes);
        };
    }

    /**
     * @return \Closure
     */
    public function toMimeType()
    {
        return function ($sizeInBytes = null) {
            /* @var $this \Illuminate\Support\Stringable */
            $sizeInBytes = is_null($sizeInBytes) ? $this->value : $sizeInBytes;
            $sizeInBytes = (int)$sizeInBytes instanceof Stringable ? "{$sizeInBytes}" : $sizeInBytes;
            $sizeInBytes = $sizeInBytes ?: 0;

            return $this->mimeType($sizeInBytes)->__toString();
        };
    }

    /**
     * @return \Closure
     */
    public function mimeType()
    {
        return function (string $path = null, $options = FILEINFO_MIME_TYPE) {
            $path = is_null($path) ? $this->value : $path;
            $path = (string)($path instanceof Stringable ? "{$path}" : $path);

            $finfo = $path ? (new \finfo($options))->file($path) : "";

            return new static($finfo);
        };
    }

    /**
     * @return \Closure
     */
    public function get()
    {
        return function () {
            return (string)$this->value . "";
        };
    }

    /**
     * @return \Closure
     */
    public function contains()
    {
        /**
         * Determine if a given string contains a given substring.
         *
         * @param string          $haystack
         * @param string|string[] $needles
         * @param bool            $ignore_case
         *
         * @return bool
         */
        return function ($haystack, $needles, $ignore_case = false) {
            foreach ((array)$needles as $needle) {
                if ( $ignore_case ) {
                    $needle = snake_case($needle);
                    $haystack = snake_case($haystack);
                }
                if ( $needle !== '' && mb_strpos($haystack, $needle) !== false ) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * @return \Closure
     */
    public function ends()
    {
        /**
         * Determine if a given string ends with a given substring.
         *
         * @param string          $haystack
         * @param string|string[] $needles
         *
         * @return bool
         */
        return function ($haystack, $needles) {
            foreach ((array)$needles as $needle) {
                if ( $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle ) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * @return \Closure
     */
    public function starts()
    {
        /**
         * Determine if a given string starts with a given substring.
         *
         * @param string          $haystack
         * @param string|string[] $needles
         *
         * @return bool
         */
        return function ($haystack, $needles) {
            foreach ((array)$needles as $needle) {
                if ( (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0 ) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * @return \Closure
     */
    public function containsAll()
    {
        /**
         * Determine if a given string contains all array values.
         *
         * @param string   $haystack
         * @param string[] $needles
         *
         * @return bool
         */
        return function ($haystack, array $needles)
        {
            foreach ($needles as $needle) {
                if ( !$this->contains($haystack, $needle) ) {
                    return false;
                }
            }

            return true;
        };
    }

    /**
     * @return \Closure
     */
    public function removeNumbers()
    {
        return function (string $str) {
            return preg_replace('/[0-9]+/', '', $str);
        };
    }

    /**
     * @return \Closure
     */
    public function onlyNumbers()
    {
        return function (string $str) {
            return preg_replace('/[^0-9]/', '', $str);
        };
    }

}
