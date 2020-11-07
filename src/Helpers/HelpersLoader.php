<?php
/**
 * Copyright آ© 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

/**
 * defaine LOAD_PATH as custom path
 */

$app_helpers_path = defined('LOAD_PATH') ? LOAD_PATH : dirname(str_before(__DIR__, DIRECTORY_SEPARATOR . 'src')) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . '' . 'Helpers'. DIRECTORY_SEPARATOR .'src';

/**
 *
 */
define('HELPERS_DIR', __DIR__ . DIRECTORY_SEPARATOR . '');
/**
 *
 */
define('APP_HELPERS_DIR', $app_helpers_path);

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
        $_helpers_dir =
            ($helpers_dir &&
                file_exists($helpers_dir) &&
                is_readable($helpers_dir) &&
                is_dir($helpers_dir))
                ? $helpers_dir : false;

        if ( !$_helpers_dir ) {
            if( isRunningInConsole() ) {
                dump(" # Failed to load Path: {$helpers_dir}" );
            }

            return;
        }

        $this->path = $helpers_dir = $_helpers_dir;

        /** @var Collection $files */
        $files = toCollect((new Filesystem)->files($this->path));

        /**
         * @var \Symfony\Component\Finder\SplFileInfo $f
         */
        $files->map(static function (\Symfony\Component\Finder\SplFileInfo $f) use ($helpers_dir) {
            if ( !in_array($f->getRealPath(), self::$included, true) ) {
                if ( ends_with(pathinfo($f->getFilename(), PATHINFO_FILENAME), self::ALLOWED_SUFFIX) ) {
                    if ( "." . $f->getExtension() === self::ALLOWED_EXTENSION ) {
                        if ( $f->isFile() && $f->isReadable() ) {
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

new HelpersLoader(HELPERS_DIR . 'src-interfaces');
new HelpersLoader(HELPERS_DIR . 'src-traits');
new HelpersLoader(HELPERS_DIR . 'src');
new HelpersLoader(HELPERS_DIR . 'src-class');
new HelpersLoader(HELPERS_DIR . 'macro');

foreach ((array)APP_HELPERS_DIR as $path) {
    new HelpersLoader($path);
}
