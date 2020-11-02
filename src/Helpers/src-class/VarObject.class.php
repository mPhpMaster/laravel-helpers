<?php

/**
 * Class VarObject
 */
class VarObject implements hasToString, Stringable, Jsonable, Arrayable
{
    use THasToString;

    /**
     * @var mixed|null
     */
    private $key = null;
    /**
     * @var mixed|null
     */
    private $value = null;

    /**
     * @var array
     */
    public $data;

    /**
     * VarObject constructor.
     *
     * @param mixed      $value
     * @param mixed|null $key
     */
    public function __construct($value, $key = null, Closure $on_update = null)
    {
        $this->onUpdate(function ($self) {
            $self->data = $self->get(true);
        });
        $on_update && $this->onUpdate($on_update);
        $this->set($key, $value);
    }

    /**
     * @param string $newValue
     *
     * @return mixed
     */
    public function value($newValue = self::class)
    {
        if ( $newValue !== self::class ) {
            $this->value = &$newValue;
            $this->onUpdate();
        }

        return getValue($this->value, $this);
    }

    /**
     * @param string $newKey
     *
     * @return mixed
     */
    public function key($newKey = self::class)
    {
        if ( $newKey !== self::class ) {
            $this->key = &$newKey;
            $this->onUpdate();
        }

        return getValue($this->key, $this);
    }

    /**
     * @return static
     */
    public static function make()
    {
        return new static(...func_get_args());
    }

    /**
     * @param \Closure|bool|null $callback
     *
     * @return mixed|null
     */
    public function get($callback = null)
    {
        if ( $callback ) {
            $callback = is_numeric($callback) ? (bool)$callback : $callback;
            $callback = is_bool($callback) && $callback === true ? function ($v, $k) {
                return is_null($k) ? [$v] : [$k => $v];
            } : $callback;
            $callback = isClosure($callback) ? $callback($this->value(), $this->key()) : $this->value;
        } else {
            $callback = $this->value;
        }

        return $callback;
    }

    /**
     * @param      $key
     * @param null $value
     *
     * @return $this
     */
    public function set($key, $value = null)
    {
        if ( func_num_args() === 1 ) {
            if ( is_array($key) ) {
                if ( count($key) === 1 ) {
                    $this->key = key($key);
                    $this->value = &$key[ $this->key ];
                } else if ( count($key) === 2 ) {
                    $this->key = head($key);
                    $this->value = last($key);
                } else {
                    $this->value = &$key;
                }

                return $this->onUpdate();
            }

            $this->value = &$key;
        } else if ( func_num_args() === 2 ) {
            $this->key = &$key;
            $this->value = &$value;
        }

        return $this->onUpdate();
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        $this->value = null;
        $this->key = null;
        $this->onUpdate();
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->key == $name && isset($this->value);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
        $this->onUpdate();
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        if ( $this->key == $name || strtolower($name) === 'self' ) {
            $var = &$this->value;
            return $var;
        }

        return null;
    }

    /**
     * @param string $method
     * @param array  $args
     */
    public function __call($method, $args)
    {
        if ( str_start($method, "is") ) {
            if ( function_exists($_method = snake_case($method)) || function_exists($_method = studly_case($method)) ) {
                return call_user_func_array($_method, [$this->value()]);
            } elseif ( $_type = snake_case(str_after($method, 'is')) ) {
                $check_type = strtolower(gettype($this->value())) === strtolower($_type);
                $_type = studly_case($_type);
                $check_class = (
                    class_exists($_type) ||
                    interface_exists($_type) ||
                    trait_exists($_type) ||
                    function_exists($_type)
                ) ? ($this->value() instanceof $_type) : false;
                dE(
                    [
                        $_type,
                        getRealClassName($_type),
                        $this->value()
                    ],
                    $check_class,
                    $this->value() instanceof $_type
                );

                return $check_type || $check_class || false;
            }
        }

        throw_if(!method_exists($this, $method) && !function_exists($method), new BadMethodCallException("Method $method does not exist."), $args);

        return call_user_func_array(method_exists($this, $method) ? [$this, $method] : $method, $args);
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)(is_array($result = $this->get()) ? $this->toJson() : $result);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        $return = is_null($this->key) ? $this->value() : [$this->key() => $this->value()];

        return json_encode(is_array($return) ? $return : [$return], $options);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return is_null($this->key) ? [$this->value] : [$this->key => $this->value];
    }

    /**
     * @param \Closure|null $closure
     *
     * @return $this
     */
    public function onUpdate(Closure $closure = null)
    {
        static $current_closures = [];

        if ( is_null($closure) ) {
            foreach ($current_closures as $_closure) {
                call_user_func_array($_closure, [&$this]);
            }
        } else {
            if ( !isClosure($closure) ) {
                $closure = function () use (&$closure) {
                    return $closure;
                };
            }
            $current_closures[] = &$closure;
        }

        return $this;
    }

    /**
     * @param \Closure|null $callback
     *
     * @return \Closure
     */
    public function wrap(Closure $callback = null)
    {
        $callback = !is_null($callback) ? $callback : fn($v) => $v;
        $value = $this->get(true);
        $instance = function () use ($value, $callback) {
            return $callback($value);
        };
        return $instance->bindTo($this, $this);
    }
}
