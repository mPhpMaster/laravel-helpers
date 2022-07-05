<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use MPhpMaster\LaravelHelpers\Traits\TForwardsCallToInstance;
use MPhpMaster\LaravelHelpers\Traits\TForwardsGetToInstance;
use MPhpMaster\LaravelHelpers\Traits\TForwardsStaticCallToInstance;
use Illuminate\Support\Traits\Tappable;

/**
 * Class InternalEvent
 */
class InternalEvent
{
    use TForwardsCallToInstance;
    use TForwardsStaticCallToInstance;
    use TForwardsGetToInstance;
    use Tappable;

    public static $events = [];

    public static function newEvent($category, $name = null, $callback = null, $obj = null)
    {
        if ( !self::$events[ $category ] ) {
            self::$events[ $category ] = [];
        }

        $newEventGenerator = static::eventGenerator();
        $newEvent = $newEventGenerator(...func_get_args());

        if ( !is_null($newEvent->name) ) {
            if ( array_key_exists($newEvent->name, self::$events[ $newEvent->category ]) ) {
                unset(self::$events[ $newEvent->category ][ $newEvent->name ]);
            }

            return self::$events[ $newEvent->category ][ $newEvent->name ] = &$newEvent;
        }

        return self::$events[ $newEvent->category ][] = &$newEvent;
    }

    public static function bind($event, $callback, $obj = null)
    {
        if ( !self::$events[ $event ] ) {
            self::$events[ $event ] = [];
        }

        self::$events[ $event ][] = ($obj === null) ? $callback : [$obj, $callback];
    }

    protected $current_category = null;

    /**
     * Find or create new event.
     *
     * @param string      $category
     * @param string|null $name
     *
     * @return \Closure|\stdClass|mixed
     */
    public static function event($category, $name = null)
    {
        $newEvent = function () use (&$category, &$name) {
            if ( func_num_args() === 2 ) {
                [$category, $name] = func_get_args();
            }
            $newEvent = static::newEvent($category, $name);

            $_newEvent = &$newEvent;
            return $_newEvent;
        };

        $instance = null;
        if ( !self::$events[ $category ] ) {
            $instance = static::newEvent($category, $name);
        } else {
            $instance = &self::$events[ $category ];
        }

        if ( is_array($instance) ) {
            if ( !is_null($name) ) {
                if ( array_key_exists($name, $instance) ) {
                    $instance = &$instance[ $name ];

                    return $instance;
                }

                $newEventGenerator = static::eventGenerator();
                $newEvent = $newEventGenerator($category, $name);

                self::$events[ $newEvent->category ][ $newEvent->name ] = &$newEvent;
                return $newEvent;
            }

            return $newEvent;
        }

        return $instance ? $instance : $newEvent;
    }

    public static function fire($event)
    {
        if ( !self::$events[ $event ] ) return;

        foreach (self::$events[ $event ] as $callback) {
            if ( call_user_func($callback) === false ) break;
        }
    }

    /**
     * Returns new instance.
     *
     * @param string|null $name Add name to stored Arguments.
     *
     * @return static
     */
    public static function make($category = null)
    {
        $newInstance = (new static);
        $newInstance->current_category = &$category;
        $instance = &$newInstance;

        return $instance;
    }

    private static function eventGenerator()
    {
        return function ($category, $name = null, $callback = null, $obj = null) {
            $newEvent = new stdClass();
            $newEvent->category = &$category;
            $newEvent->name = &$name;
            $newEvent->callback = &$callback;
            $newEvent->obj = &$obj;
            $newEvent->fire = function () use (&$newEvent) {
                if ( is_null($newEvent->obj) && is_null($newEvent->callback) ) {
                    return false;
                }

                return is_null($newEvent->obj) ? $newEvent->callback : [$newEvent->obj, $newEvent->callback];
            };

            return $newEvent;
        };
    }

    protected function forwardCallTo($name, $arguments)
    {
        dE(
            func_get_args()
        );
    }

    protected function forwardGetTo($name = null)
    {
        $instance = &static::$events;
        if ( !is_null($this->current_category) ) {
            if ( !$instance[ $this->current_category ] ) {
                $instance[ $this->current_category ] = [];
                $instance = &$instance[ $this->current_category ];
            }
        }

        if ( !is_null($name) ) {
            if($instance[ $name ]) {
                $_instance = &$instance[ $name ];
            } else {
                $instance[ $name ] = null;
                $_instance = &$instance[ $name ];
            }

            return $_instance;
        }

        return $instance;
/*
        if ( !$instance[ $name ] ) {
            $_instance = static::newEvent($this->current_category, $name);
            unset($instance);
            $instance = &$_instance;
        }

        $instance = &$instance[ $this->current_category ];

        if ( method_exists($this, $name) ) {
            return $this->{$name}(...$arguments);
        }

        return static::$events[ $name ] ?? (static::$events[ $name ] = [])*/
    }

    protected function forwardStaticCallTo($name, $arguments)
    {
        if(method_exists($this, $name)) {
            return call_user_func([$this, $name], $arguments);
        }

        dE(
            func_get_args()
        );
    }

    public function __invoke($category = null, $name = null)
    {
        if ( func_num_args() === 2 ) {
            [$category, $name] = func_get_args();
        }

        if ( is_null($category) ) {
            if($this->current_category) {
                $event = $this->__invoke($this->current_category, $name);
                $_event = &$event;

                return $_event;
            }

            return function ($category = null, $_name = null) use (&$name){
                return $this->__invoke($category, $_name ?? $name);
            };
        }

        $this->current_category = &$category;
        if ( is_null($category) ) {
            return function ($category = null, $name = nul) {

            };
        }

        return static::event($this->current_category, $name);
    }
}
