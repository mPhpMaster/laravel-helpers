<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Traits\ForwardsCalls;
use MPhpMaster\LaravelHelpers\Optional;
use MPhpMaster\LaravelHelpers\Traits\{TForwardsAllCallsToInstance, TForwardsGetToInstance, TMakeMethod};

/**
 * Class HigherOrderProxy
 * Foreword calls and gets to instance using methods.
 *
 * @mixin TForwardsGetToInstance
 * @mixin TForwardsAllCallsToInstance
 */
class HigherOrderProxy extends Optional
{
    use TForwardsGetToInstance;
    use TForwardsAllCallsToInstance;
    use TMakeMethod;
    use ForwardsCalls;

    /**
     * @var \HigherOrderProxy|mixed
     */
    protected $value;
    /**
     * @var string|null
     */
    protected $getMethod;
    /**
     * @var string|null
     */
    protected $callMethod;

    /**
     * HigherOrderProxy constructor.
     *
     * @param mixed|null  $value
     * @param string|null $getMethod
     * @param string|null $callMethod
     */
    public function __construct($value, ?string $getMethod = null, ?string $callMethod = null)
    {
        try {
            parent::__construct($value);
            static::_make($this, $value, $getMethod, $callMethod);
        } catch (Exception $exception) {

        }
    }

    /**
     * @param mixed|null  $value
     * @param string|null $getMethod
     * @param string|null $callMethod
     * @param mixed[]     ...$arguments
     *
     * @return static
     */
    public static function make(...$arguments)
    {
        return parent::make(...$arguments);
    }

    /**
     * @param static|null $object
     * @param mixed|null  $instance
     * @param string|null $getMethod
     * @param string|null $callMethod
     *
     * @return static
     * @throws \Throwable
     */
    protected static function _make($object, $instance, ?string $getMethod = null, ?string $callMethod = null)
    {
        /** @var static $object */
        $object ??= static::make($instance);

        $getMethod ??= null;
        $callMethod ??= null;
        if ( is_callable($instance) ) {
            $object->value(value($instance));

        } else if ( is_object($instance) ) {
            $object->value($instance);

        } else if ( is_string($instance) ) {
            throw_unless(class_exists($instance),
                \Symfony\Component\ErrorHandler\Error\ClassNotFoundError::class,
                ["Class not exists! [{$instance}]", null]
            );

            $object->value($instance);

        } else {
            $object->value($object);

        }
        $object->value(toCollect($object->value())->all());

        $object->setGetMethod($getMethod);
        $object->setCallMethod($callMethod ?? 'call');

        return $object;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function forwardAllCallsTo(...$arguments)
    {
        $method = $this->callMethod ?? 'call';
        if ( is_object($this->value) ) {
            if ( is_callable($this->value) ) {
                return call_user_func_array($this->value, $arguments);
            }

            if ( is_callable([$this->value, $method]) ) {
                return $this->forwardCallTo($this->value, $method, $arguments);
            }

        }

        if ( is_callable($method) ) {
            return getValue($method, $arguments);
        }

        return $this->forwardCallTo($this, $method, $arguments);
    }

    /**
     * @return mixed
     */
    public function forwardGetTo($name)
    {
        if ( $this->getMethod ) {
            return $this->value->{$this->getMethod}($name);
        }

        return $this->value->{$name};
    }

    /**
     * @param mixed|null $value
     *
     * @return static|mixed|null
     */
    public function value($value = null)
    {
        if ( func_num_args() === 0 ) {
            return $value;
        }

        $this->value = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGetMethod(): ?string
    {
        return $this->getMethod;
    }

    /**
     * @param string|null $getMethod
     *
     * @return static
     */
    public function setGetMethod(?string $getMethod)
    {
        $this->getMethod = $getMethod;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCallMethod(): ?string
    {
        return $this->callMethod;
    }

    /**
     * @param string|null $callMethod
     *
     * @return static
     */
    public function setCallMethod(?string $callMethod)
    {
        $this->callMethod = $callMethod;

        return $this;
    }
}
