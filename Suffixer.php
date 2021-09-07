<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers;

/**
 * Class Suffixer
 * @version 0.1
 * @package MPhpMaster\LaravelHelpers
 */
class Suffixer implements \Countable
{
    public $string = null;
    public $suffixDelimiter = '::';

    public function __construct($string = null, $delimiter = '::')
    {
        $this->update(...func_get_args());
    }

    public function update($string = null, $delimiter = '::')
    {
        if (func_num_args() >= 2)
            $this->suffixDelimiter = is_null($delimiter) ? '' : $delimiter;

        if (func_num_args() > 0)
            $this->string = is_null($string) ? $this->string : $string;

        return $this;
    }

    /**
     * Returns Controller name as string, add method name if sent.
     *
     * @param string|null $method
     *
     * @return string
     */
    public function get($method = '')
    {
        return str_suffix($this->string, $this->suffixDelimiter, $method);
    }

    public static function make($string)
    {
        return $string instanceof Suffixer ?
            $string->update(...func_get_args()) :
            new static(...func_get_args());
    }

    /**
     * @param mixed ...$parameters
     *
     * @return \Closure
     */
    public static function makeer(...$parameters)
    {
        return function ($string) use (&$parameters) {
//            ($string != 'DayController') && dd($string, ...$parameters);
            return new Suffixer($string, ...$parameters);
        };
    }

    public function __toString()
    {
        return $this->get();
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        try {
            return isUsed($this->{$name});
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return !!($this->__toString());
    }
}
