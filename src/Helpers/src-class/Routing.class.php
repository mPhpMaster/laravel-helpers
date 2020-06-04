<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Traits\Tappable;
use mPhpMaster\Support\Traits\TForwardsAllCallsToInstance;

/**
 * Class Routing
 *
 * Foreword calls to Route and register calls.
 *
 * @mixin \AbstractRouting
 */
class Routing
{
    use TForwardsAllCallsToInstance;
    use Tappable;

    /**
     * Stored arguments.
     *
     * @var array
     */
    public $args = [];

    /**
     * Returns new instance.
     *
     * @param string|null $name Add name to stored Arguments.
     *
     * @return static
     */
    public static function make(?string $name = null)
    {
        return (new static)->addName($name);
    }

    /**
     * Call Relay.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    function forwardAllCallsTo($name, $arguments = [])
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        return (new AbstractRouting)->{$name}(...$arguments);
    }

    /**
     * Returns instance with the given arguments.
     *
     * @param mixed ...$arguments
     *
     * @return static
     */
    public static function makeCallWith(...$arguments)
    {
        return tap(new static, function ($caller) use ($arguments) {
            foreach ((array) $arguments as $key => $_arguments) {
                if(!isset($_arguments['controller'])) {
                    $_arguments['controller'] = "\\" . Controller::class;
                }
                $arguments[ $key ] = $_arguments;
            }

            $caller->args = $arguments;
        });
    }

    /**
     * Apply the existing arguments to the given callable.
     *
     * @param callable $callable
     *
     * @return static
     */
    public function callWith(callable $callable)
    {
        return tap($this, function ($caller) use ($callable) {
            call_user_func_array($callable, $caller->args);
        });
    }

// region: OOP Controller
    /**
     * Get the existing args/arg.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getArg(?string $name = null)
    {
        if(count($this->args) == 0) return null;

        return is_null($name) ? $this->args[0] : array_get($this->args[0], $name, null);
    }

    /**
     * Add info into existing args.
     *
     * @param string $name
     * @param string|null $value
     *
     * @return static
     */
    public function appendArg(string $name, ?string $value)
    {
        if(count($this->args) == 0) $this->args[0] = [];

        $this->args[0][ $name ] = $value;
        if(is_null($value) && array_key_exists($name, $this->args[0])) {
            unset($this->args[0][ $name ]);
        }

        return $this;
    }

    /**
     * Add Model name.
     *
     * @param string|null $value
     *
     * @return static
     */
    public function addName(?string $value)
    {
        return $this->tap(function ($self) use($value) {
            $self->appendArg('name', $value);
        });
    }

    /**
     * Add Model.
     *
     * @param string|null $value
     *
     * @return static
     */
    public function addModel(?string $value)
    {
        return $this->tap(function ($self) use($value) {
            $self->appendArg('model', $value);
        });
    }

    /**
     * Add Repository.
     *
     * @param string|null $value
     *
     * @return static
     */
    public function addRepo(?string $value)
    {
        return $this->tap(function ($self) use($value) {
            $self->appendArg('repo', $value);
        });
    }

    /**
     * Add Controller.
     *
     * @param string|null $value
     *
     * @return static
     */
    public function addController(?string $value)
    {
        return $this->tap(function ($self) use($value) {
            $self->appendArg('controller', $value);
        });
    }

    /**
     * Register Resource for the given data.
     *
     * @param array|null $args Overwrite existing info.
     *
     * @return static
     */
    public function registerResource(?array $args = null)
    {
        if(!is_null($args) && !empty($args)) {
            $this->args = count($this->args) == 0 ? [] : $this->args;
            $this->args[0] = $args;
        }

        return $this->tap(function ($self) {
            $controller = $self->getArg('controller');
            if(!$controller) {
                $controller = "\\" . Controller::class;
            }

            Routing::resource($self->getArg('name'), $controller);
        });
    }

    /**
     * Register Model for the given data.
     *
     * @param array|null $args Overwrite existing info.
     *
     * @return static
     */
    public function registerModel(?array $args = null)
    {
        if(!is_null($args) && !empty($args)) {
            $this->args = count($this->args) == 0 ? [] : $this->args;
            $this->args[0] = $args;
        }

        return $this->tap(function ($self) {
            Routing::model($self->getArg('name'), $self->getArg());
        });
    }
// endregion: OOP Controller

}
