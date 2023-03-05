<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers;

use Illuminate\Support\Traits\Tappable;
use MPhpMaster\LaravelHelpers\Traits\TMacroable;

/**
 * Class ClassAccessWrapper
 */
class ClassAccessWrapper
{
    use Tappable,
        TMacroable;

    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    protected mixed $_self;
    /**
     * @var \ReflectionObject
     */
    protected \ReflectionObject $_refl;

    /**
     * @param $self
     */
    public function __construct($self)
    {
        if( !is_object($self) ) {
            $self = app($self);
        }
        $this->_self = $self;
        $this->_refl = new \ReflectionObject($self);
    }

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function __call($method, $args)
    {
        if ( ($result = $this->handleMacroCall($method, $args)) && $result !== static::$MACRO_NOT_FOUND ) {
            return $result;
        }

        $mrefl = $this->_refl->getMethod($method);
        $mrefl->setAccessible(true);
        return $mrefl->invokeArgs($this->_self, $args);
    }

    /**
     * @param $name
     * @param $value
     *
     * @return void
     * @throws \ReflectionException
     */
    public function __set($name, $value)
    {
        $prefl = $this->_refl->getProperty($name);
        $prefl->setAccessible(true);
        $prefl->setValue($this->_self, $value);
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function __get($name)
    {
        $prefl = $this->_refl->getProperty($name);
        $prefl->setAccessible(true);
        return $prefl->getValue($this->_self);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        $value = $this->__get($name);
        return isset($value);
    }
}
