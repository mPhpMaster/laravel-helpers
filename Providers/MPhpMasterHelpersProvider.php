<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Class MPhpMasterHelpersProvider
 *
 * @package MPhpMaster\LaravelHelpers\Providers
 */
class MPhpMasterHelpersProvider extends ServiceProvider
{
    const MIXINS_DIR__ = __DIR__ . '/../mixins';

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
            function($attribute, $value, $parameters, $validator) {
                $value = trim($value, ' +.');

                return Str::startsWith($value, "05") && strlen($value) === 10;
            }
        );

        \Illuminate\Support\Facades\Validator::extend(
            'phone',
            static function($attribute, $value, $parameters) {
                return
                    (strlen($value) === 7 || strlen($value) === 10 || strlen($value) === 9)
                    && is_numeric($value);

                return strlen($value) === 7 && substr($value, 0, 2) == '01';

                return preg_match("/^([0-9\s\-\+\(\)]*)$/", $value);
            }
        );

        $this->app->singleton('extra-macros', function() {
            return new \MPhpMaster\LaravelHelpers\ExtraMacros();
        });

        $this->app->singleton('cached-response', function() {
            return new \CachedResponse();
        });

        /**
         * Helpers
         */
        require_once __DIR__ . '/../Helpers/HelpersLoader.php';

        if( config('app.debug') || config('app.models_to_functions') ) {
            new \CustomFunctions;
        }
    }

    /**
     *
     */
    public function registerMacros()
    {

        if( !function_exists('cutBasePath') ) {
            $cutBasePath = static function($fullFilePath = null, $prefix = '') {
                $fullFilePath = $fullFilePath ?:
                    Arr::get(@current(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)), 'file', null);

                return $prefix . str_ireplace(base_path() . DIRECTORY_SEPARATOR, '', $fullFilePath ?: __FILE__);
            };
        } else {
            $cutBasePath = 'cutBasePath';
        }

        $namespace = implode('\\', array_slice(explode('/', getCurrentNamespace() ?: static::class), 0, 2));
        // info: fixing issue for windows
        $__getNamespace = function($path) use ($cutBasePath) {
            return str_ireplace('/', NAMESPACE_SEPARATOR, $cutBasePath($path));
        };
        Collection::make(
            // info: fixing issue for windows
            // glob(real_path($cutBasePath(self::MIXINS_DIR__ . "/*Invoke.php")))
            glob(fixPath(real_path($cutBasePath("/*Invoke.php", self::MIXINS_DIR__))))
        )
                  ->mapWithKeys(static function($path) use ($namespace) {
                      $file_name = pathinfo($path, PATHINFO_FILENAME);

                      return [
                          "{$namespace}\\mixins\\" . $file_name => Str::replaceLast('Invoke', '', $file_name),
                      ];
                  })
                  ->reject(static function($macro, $path) use ($cutBasePath, $__getNamespace) {
                      return Collection::hasMacro($macro) || !class_exists($__getNamespace($path));
                  })
                  ->each(static function($macro, $path) use ($cutBasePath, $__getNamespace) {
                      $class = $__getNamespace($path);

                if( class_exists($class) ) {
                    try {
                      Collection::macro(Str::camel($macro), app($class)());
                    } catch(\Exception $exception) {

                    }
                }
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
