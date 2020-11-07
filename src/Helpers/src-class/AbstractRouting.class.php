<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Support\Facades\Route;


/**
 * Class Routing
 *
 * Foreword calls to Route and register calls.
 */
class AbstractRouting
{

    /**
     * block entity to register by name
     *
     * @var array
     */
    const BLOCKED = [
        // blocked models (prevent register)
        'model' => [
//            Model Name,
        ],

        // blocked controllers (prevent register)
        'controller' => [
            // Api Controllers
            'Api' => [
//                Controller Name,
                'Auth'
            ],

            // Global Controllers
            '.' => [
//                Controller Name,
            ],
        ],

        // blocked repositories (prevent register)
        'repositories' => [
//            Repository Name,
        ],
    ];

    /**
     * registered data.
     * @var array
     */
    public static $registered = [
        'model' => [],
        'controller' => [
            // Api Controllers
            'Api' => [],

            // Global Controllers
            '.' => [],
        ],
        'repositories' => [],
    ];

    /**
     * Register a model binder for a wildcard.
     *
     * @param string $key
     * @param string|Arrayable $class
     * @param \Closure|null $callback
     *
     * @return static
     */
    public function model($key, $class, Closure $callback = null)
    {
        $modelInfo = [];
        if (!is_string($class) && isArrayableOrArray($class)) {
            $modelInfo = valueToArray($class);
            try {
                $class = $modelInfo['model'];
            } catch (Exception $exception ) {
                dE(
                    [__FILE__,__LINE__],
                    data_get($modelInfo, 'model'),
                    $modelInfo,
                    $class
                );
            }
        }

        $denied = array_search($class, static::BLOCKED['model']) > -1;

        if (
            !$denied &&
            !isset(static::$registered['model'][$class])
        ) {
            static::$registered['model'][$class] = func_get_args();
//            static::$registered['info'][$key] = array_key_exists($key, static::$registered['info']) ? static::$registered['info'][$key] : [];
//            static::$registered['info'][$key] = array_merge(static::$registered['info'][$key], $modelInfo);

            Route::model($key, $class, $callback);
        }

        return $this;
    }

    /**
     * Register a resource controller.
     *
     * @param string $name
     * @param string $controller
     * @param array  $options
     *
     * @return PendingResourceRegistration
     */
    public function resource($name, $controller, array $options = [])
    {
//        $options['as'] = "routeName@" . ($options['as'] ?? "");
        return Route::resource($name, $controller, $options);
    }

    /**
     * Register a Controller name & namespace for autoload.
     *
     * @param        $name
     * @param string $namespace
     * @param null $tag
     */
    public function controller($name, $namespace = "\\", $tag = '.')
    {
        $denied = isset(static::BLOCKED['controller'][$tag ?: '.']) && array_search($name, static::BLOCKED['controller'][$tag ?: '.']) > -1;

        if (!$denied && !isset(static::BLOCKED['controller'][$tag ?: '.'][$name])) {
            static::$registered['controller'][$tag ?: '.'][$name] = $namespace;
        }
    }

    /**
     * HELPER: Register controllers names & namespace
     *
     * @param          $dir
     * @param callable $callable
     */
    public function loadControllers($dir, callable $callable)
    {
        collect((new Filesystem)->allFiles(real_path($dir . "../Http/Controllers")))->map(function ($v) use (&$callable) {
            /** @var \Symfony\Component\Finder\SplFileInfo $v */
            if ($v->getExtension() === 'php')
                static::controller(...$callable($v));
        });
    }

    /**
     * Register a Repository name & namespace for autoload.
     *
     * @param string $modelName
     * @param string $name
     * @param string $namespace
     */
    public function repository($modelName, $name, $namespace = "\\")
    {
        $denied = isset(static::BLOCKED['repositories']) && array_search($name, static::BLOCKED['repositories']) > -1;

        if (!$denied && !isset(static::BLOCKED['repositories'][$name])) {
            static::$registered['repositories'][$name] = [$modelName => $namespace];
        }
    }

    /**
     * HELPER: Register Repositories names & namespace
     *
     * @param          $dir
     * @param callable $callable
     */
    public function loadRepositories($dir, callable $callable)
    {
        collect((new Filesystem)->allFiles(real_path($dir . "../Repository/")))->map(function ($v) use (&$callable) {
            /** @var \Symfony\Component\Finder\SplFileInfo $v */
            if ($v->getExtension() === 'php' && str_finish(pathinfo($v->getFilename(), PATHINFO_FILENAME), 'Repository') && pathinfo($v->getFilename(), PATHINFO_FILENAME) != 'Repository') {
                static::repository(...$callable($v));
            }
        });
    }

    /**
     * @param string $abstract
     * @return mixed
     */
    public static function getModel(string $abstract)
    {
        $allRegistred = collect(static::$registered['model'])->mapWithKeys(function ($v, $k) {
            return [$v[0] => $v[1]];
        });

        return $allRegistred->get($abstract, null);
    }

    /**
     * @param string $abstract
     * @return mixed
     */
    public static function getController(string $abstract)
    {
        $allRegistred = collect(static::$registered['controller'])->mapWithKeys(function ($v, $k) {
            return [$v[0] => $v[1]];
        });

        return $allRegistred->get($abstract, null);
    }

    /**
     * @param string $abstract
     * @return mixed
     */
    public static function getRepository(string $abstract)
    {
        $allRegistred = collect(static::$registered['repositories'])->mapWithKeys(function ($v, $repoName) {
            $name = key($v);
            $repoNamespace = data_get($v, $name);
            $repoPath = trim(str_ireplace( "/", "\\", "{$repoNamespace}/{$repoName}"), "\\");
            return [$name => $repoPath];
        });

        return $allRegistred->get($abstract, null);
    }
}
