<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

/**
 *
 */
define( 'HELPERS_DIR', __DIR__ . DIRECTORY_SEPARATOR . '' );

/**
 * Class HelpersLoader
 */
class HelpersLoader
{
    /**
     * @ignore
     */
    public const
        ALLOWED_SUFFIX = [
        '.functions',
        '.class'
    ],
        ALLOWED_EXTENSION = '.php';

    /**
     * @var null|string
     */
    private
        $path;

    /**
     * @var array
     */
    protected
    static $included = [];

    /**
     * HelpersLoader constructor.
     *
     * @param null $helpers_dir
     */
    /**
     * HelpersLoader constructor.
     *
     * @param null $helpers_dir
     */
    public function __construct($helpers_dir = null)
    {
        $helpers_dir =
            ($helpers_dir &&
                file_exists($helpers_dir) &&
                is_readable($helpers_dir) &&
                is_dir($helpers_dir))
                ? $helpers_dir : HELPERS_DIR;

        if (!$helpers_dir) {
            debug( [ "Failed to load Path: " => $helpers_dir ] );
        }

        $this->path = $helpers_dir;

        /** @var Collection $files */
        $files = toCollect(File::files($this->path));

        /**
         * @var \Symfony\Component\Finder\SplFileInfo $f
         */
        $files->map( static function (\Symfony\Component\Finder\SplFileInfo $f) {
            if (!in_array( $f->getRealPath(), self::$included, true ) ) {
                if( ends_with( $f->getFilenameWithoutExtension(), self::ALLOWED_SUFFIX ) ) {
                    if( "." . $f->getExtension() === self::ALLOWED_EXTENSION ) {
                        if( $f->isFile() && $f->isReadable() ) {
                            include_once $f->getRealPath();
                            self::$included[] = $f->getRealPath();
                        }
                    }
                }
            }
        });
        self::$included = array_unique(self::$included);
    }

}

new HelpersLoader( HELPERS_DIR . 'src' );
new HelpersLoader( HELPERS_DIR . 'src-class' );
new HelpersLoader( HELPERS_DIR . 'macro' );
