<?php

use App\Traits\{ForwardsAllCallsToInstance, ForwardsGetToInstance};
use mPhpMaster\Support\Optional;

/**
 * Class HigherOrderProxy
 *
 * Foreword calls and gets to instance using methods.
 *
 * @mixin ForwardsGetToInstance
 * @mixin ForwardsAllCallsToInstance
 */
class HigherOrderProxy extends Optional {
    use ForwardsGetToInstance;
    use ForwardsAllCallsToInstance;

    protected $instance;
    /**
     * @var string|null
     */
    protected $getMethod;
    /**
     * @var string|null
     */
    protected $callMethod;

    public function __construct($instance, ?string $getMethod = null, ?string $callMethod = null)
    {
        if(is_callable($instance)) {
            $this->instance = value($instance);

        } else if(is_object($instance)) {
            $this->instance = $instance;

        } else if(is_string($instance)) {
            throw_unless(class_exists($instance),
                \Symfony\Component\ErrorHandler\Error\ClassNotFoundError::class,
                ["Class not exists! [{$instance}]", null]
            );

            $this->instance = new $instance;

        } else {
            $this->instance = $this;

        }

        $this->getMethod = $getMethod;
        $this->callMethod = $callMethod ?? 'call';
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    function forwardAllCallsTo(...$arguments)
    {
        $method = $this->callMethod ?? 'call';
        if(is_object($this->instance)) {
            if(is_callable($this->instance)) {
                return call_user_func_array($this->instance, $arguments);
            }

            return $this->instance->{$method}(...$arguments);
        }

    }

    /**
     * @return mixed
     */
    public function forwardGetTo($name)
    {
        if($this->getMethod) {
            return $this->instance->{$this->getMethod}($name);
        }

        return $this->instance->{$name};
    }
}
