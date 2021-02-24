<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Tappable;
use mPhpMaster\Support\Traits\TMacroable;

/**
 * Class Bag
 */
class Bag implements Arrayable
{
    use Tappable,
        TMacroable;

    /**
     * @var array
     */
    private $functions = [];
    /**
     * @var array
     */
    private $vars = [];

    /**
     * Bag constructor.
     *
     * @param mixed ...$data contains methods & vars.
     */
    public function __construct(...$data)
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        if(isset($this->functions[ $name ])) {
            unset($this->functions[ $name ]);
        } else if(isset($this->vars[ $name ])) {
            unset($this->vars[ $name ]);
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->functions[ $name ]) || isset($this->vars[ $name ]);
    }

    /**
     * @param string|mixed $name
     * @param Closure|mixed $data
     */
    public function __set($name, $data)
    {
        if ( isClosure($data) )
            $this->functions[ $name ] = &$data;
        else
            $this->vars[ $name ] = &$data;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        if ( isset($this->vars[ $name ]) ) {
            $var = &$this->vars[ $name ];
            return $var;
        }

        if ( isset($this->functions[ $name ]) ) {
            $var = &$this->functions[ $name ];
            return $var;
        }

        return null;
    }

    /**
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args)
    {
        if ( ($result = $this->handleMacroCall($method, $args)) && $result !== static::$MACRO_NOT_FOUND ) {
            return $result;
        }

        throw_unless(isset($this->functions[ $method ]), new BadMethodCallException("Method $method does not exist."), $args);

        return call_user_func_array($this->functions[ $method ], $args);
    }

    public function toArray()
    {
        // TODO: Implement toArray() method.
    }
}
