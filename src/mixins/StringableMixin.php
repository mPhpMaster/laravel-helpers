<?php
/*
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace mPhpMaster\Support\mixins;

use Illuminate\Support\Stringable;

/**
 * Class StringableMixin
 *
 * @mixin \Illuminate\Support\Stringable
 * @mixin \Illuminate\Support\Str
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
