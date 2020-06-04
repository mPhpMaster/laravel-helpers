<?php

namespace mPhpMaster\Support;

/**
 * Class With
 *
 * @package mPhpMaster\Support
 */
class With
{
    private $self;
    private $callback;
    public $last = null;

    /**
     * With constructor.
     * @param array $self
     * @param callable|null $callback
     */
    public function __construct(array $self, callable $callback = null)
    {
        $is_assoc = collect($self)->keys()->filter(function ($v, $k): bool { return !is_numeric($k); })->isNotEmpty();
        $self = $is_assoc ? [$self] : $self;

        $this->self = $self;
        $this->setCallBack($callback);
    }

    /**
     * @param mixed ...$args
     *
     * @return array|array[]
     */
    public function __invoke(...$args)
    {
        return $this->call(...$args)->getSelf();
    }

    /**
     * @param mixed ...$args
     * @return $this
     */
    public function call(...$args): With
    {
        $method = $this->getCallBack();

        if(is_callable($method)) {
            $this->last = call_user_func_array($method, $args);
        }

        return $this;
    }

    /**
     * @param callable|null $method
     * @return $this
     */
    public function apply(callable $method = null): With
    {
        $self = &$this->self;
        $method = is_null($method) ? $this->getCallBack() : $method;

        if(is_callable($method)) {
            $this->last = call_user_func_array($method, collect($self)->values()->all());
        }

        return $this;
    }

    /**
     * @return array|array[]
     */
    public function getSelf()
    {
        return $this->self;

//        return $this->apply(function () { return func_get_args(); })->last;
    }

    /**
     * @return null
     */
    public function getLastResult()
    {
        return $this->last;
    }

    /**
     * @return \Closure
     */
    public function getCallBack(): \Closure
    {
        $value = &$this->self;
        $callback = &$this->callback;

        $method = function(...$arg) use(&$value, &$callback) {
            $args = array_merge(collect($value)->values()->all(), $arg);

            $callback = $callback ?: null;
            $return = is_callable($callback) ? $callback(...$args) : ($callback ?: $value);

            return $return;
        };

        return $method;
    }

    /**
     * @param null $callback
     * @return $this
     */
    public function setCallBack($callback = null): With
    {
        $this->callback = $callback && is_callable($callback) ? $callback : null;

        return $this;
    }

    /**
     * @param array $self
     * @param callable|null $callback
     * @return static
     */
    public static function make(array $self, callable $callback = null): With
    {
        return new static($self, $callback);
    }

    /**
     * @param array $self
     * @param bool|null|callable $callback
     * @return $this
     */
    public function newWith(array $self, $callback = null): With
    {
        $self = array_merge((array) $this->self, $self);
        $callback = $callback && is_callable($callback) ? $callback : (is_null($callback) ? $this->callback : null);

        return static::make($self, $callback);
    }
}