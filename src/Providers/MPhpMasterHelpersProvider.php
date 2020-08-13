<?php

namespace mPhpMaster\Support\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

/**
 * Class MPhpMasterHelpersProvider
 *
 * @package mPhpMaster\Support\Providers
 */
class MPhpMasterHelpersProvider extends ServiceProvider
{
    const DIR__ = __DIR__ . '/../mixins';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMacros();
    }

    /**
     * Bootstrap services.
     *
     * @param Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        Builder::defaultStringLength(191);
        Schema::defaultStringLength(191);


        \Illuminate\Support\Facades\Validator::extend(
            "mobile",
            function ($attribute, $value, $parameters, $validator) {
                $value = trim($value, ' +.');
                return Str::startsWith($value, "05") && strlen($value) === 10;
            }
        );

        \Illuminate\Support\Facades\Validator::extend('phone',
            static function ($attribute, $value, $parameters) {
                return
                    (strlen($value) === 7 || strlen($value) === 10 || strlen($value) === 9)
                    && is_numeric($value);
                return strlen($value) === 7 && substr($value, 0, 2) == '01';
                return preg_match("/^([0-9\s\-\+\(\)]*)$/", $value);
            }
        );

        $this->app->singleton('extra-macros', function () {
            return new \mPhpMaster\Support\ExtraMacros();
        });

        /**
         * Helpers
         */
        require_once __DIR__ . '/../Helpers/HelpersLoader.php';
        if ( config('app.debug') ) {
            new \CustomFunctions;
        }
    }

    /**
     *
     */
    public function registerMacros()
    {

        if ( !function_exists('cutBasePath') ) {
            $cutBasePath = static function ($fullFilePath = null, $prefix = '') {
                $fullFilePath = $fullFilePath ?:
                    Arr::get(@current(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)), 'file', null);

                return $prefix . str_ireplace(base_path() . DIRECTORY_SEPARATOR, '', $fullFilePath ?: __FILE__);
            };
        } else {
            $cutBasePath = 'cutBasePath';
        }

        Collection::make(
            glob(real_path($cutBasePath(self::DIR__ . "/*Invoke.php")))
        )
            ->mapWithKeys(static function ($path) {
                $file_name = pathinfo($path, PATHINFO_FILENAME);
                return [
                    "mPhpMaster\\Support\\mixins\\" . $file_name => Str::replaceLast('Invoke', '', $file_name),
                ];
            })
            ->reject(static function ($macro) {
                return Collection::hasMacro($macro);
            })
            ->each(static function ($macro, $path) use ($cutBasePath) {
                $class = str_ireplace('/', DIRECTORY_SEPARATOR, $cutBasePath($path));

                Collection::macro(Str::camel($macro), app($class)());
            });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
