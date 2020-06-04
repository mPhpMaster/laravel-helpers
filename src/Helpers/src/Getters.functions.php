<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;


if (!function_exists('getTrans')) {
    /**
     * Returns Translation or return default.
     *
     * @param string|null $lang_path lang path
     * @param null|mixed $default default value to return if trans not exists
     *
     * @return mixed
     */
    function getTrans($lang_path, $default = null)
    {
        $trans = ($trans = __($lang_path)) != $lang_path ? $trans : $default;

        return $trans;
    }
}

if (!function_exists('cutBasePath')) {
    /**
     * Remove base_path() from the given file path.
     *
     * @param string $fullFilePath file path
     * @param string $prefix any text to prefix the result with.
     *
     * @return string
     */
    function cutBasePath($fullFilePath = null, $prefix = '')
    {
        $fullFilePath = $fullFilePath ?:
            Arr::get(@current(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)), 'file', null);

        return $prefix . str_ireplace(base_path() . DIRECTORY_SEPARATOR, '', $fullFilePath ?: __FILE__);
    }
}

if (!function_exists('classPropertyValue')) {
    /**
     * Get property value fom class
     *
     * @param string $class
     * @param string $property
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    function classPropertyValue(string $class, string $property)
    {
        $_property = new ReflectionProperty($class, $property);
        $_property->setAccessible(true);
        return $_property->getValue();
    }
}

if (!function_exists("filesMap")) {
    /**
     * Get Files names into collect()->mapWithKeys()->filter()->toArray() list as [ FilenameWithoutExtension => $callabke(RealPath) ]
     *
     * @param $path
     * @param callable|null $callback
     * @param null $default
     * @return null
     */
    function filesMap($path, callable $callback = null, $default = null)
    {
        $data = null;
        $path = (new Filesystem)->exists($path) ? $path : null;

        if ($path) {
            $data = collect((new Filesystem())->files($path))->mapWithKeys(function ($v) use ($callback) {
                /** @var $v \Symfony\Component\Finder\SplFileInfo */
                $map = [$v->getFilenameWithoutExtension() => $v->getRealPath()];
                return is_callable($callback) ? $callback($map) : $map;
            })->filter()->toArray();
        }

        return $data ?: ($default ?: null);
    }
}

if (!function_exists("getByKey")) {
    /**
     * @param $data
     * @param null $key
     * @return array|mixed|null
     */
    function getByKey($data, $key = null)
    {
        iF(is_null($key)) {
            return $data;
        }
        $data = valueToArray($data ?: []);

        if($key && array_has($data, $key)) {
            $data = array_get($data, $key, []);
        }

        return $data;
    }
}

if (! function_exists('getOld')) {
    /**
     * Retrieve an old input item.
     *
     * @param string|null $key
     * @param Model|null $model
     * @param mixed $default
     *
     * @return mixed
     */
    function getOld($key, $model = null, $default = null)
    {
        $model = test($model, currentModel());
        $old = old($key, $model ? $model->{$key} : $default);

        return is_null($model) ? $default : trim($old);
    }
}