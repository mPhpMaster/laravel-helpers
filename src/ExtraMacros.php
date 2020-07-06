<?php
/**
 * Copyright (c) $year. By: hlaCk (https://github.com/mPhpMaster)
 *
 */

namespace mPhpMaster\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExtraMacros
 *
 * @mixin Builder
 *
 * @package App\Helpers
 */
class ExtraMacros
{
    /**
     * @var array
     */
    private $extra_macros = [];

    /**
     * @return array
     */
    public function getAllExtraMacros(): array
    {
        return $this->extra_macros;
    }

    /**
     * @param string   $model
     * @param string   $name
     * @param \Closure $closure
     *
     * @return $this
     */
    public function addExtraMacro(string $model, string $name, \Closure $closure): self
    {
        $this->checkModelSubclass($model);

        if (! isset($this->extra_macros[$name])) {
            $this->extra_macros[ $name ] = [];
        }

        $this->extra_macros[$name][$model] = $closure;
        $this->syncExtraMacros($name);

        return $this;
    }

    /**
     * @param        $model
     * @param string $name
     *
     * @return bool
     */
    public function removeExtraMacro($model, string $name): bool
    {
        $this->checkModelSubclass($model);

        if (isset($this->extra_macros[$name]) && isset($this->extra_macros[$name][$model])) {
            unset($this->extra_macros[$name][$model]);

            if (count($this->extra_macros[$name]) == 0) {
                unset($this->extra_macros[$name]);
            } else {
                $this->syncExtraMacros($name);
            }

            return true;
        }

        return false;
    }

    /**
     * @param $model
     * @param $name
     *
     * @return bool
     */
    public function modelHasExtraMacro($model, $name): bool
    {
        $this->checkModelSubclass($model);

        return (isset($this->extra_macros[$name]) && isset($this->extra_macros[$name][$model]));
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function modelsThatImplement($name): array
    {
        if (! isset($this->extra_macros[$name])) {
            return [];
        }

        return array_keys($this->extra_macros[$name]);
    }

    /**
     * @param $model
     *
     * @return array
     * @throws \ReflectionException
     */
    public function extraMacrosForModel($model): array
    {
        $this->checkModelSubclass($model);

        $macros = [];

        foreach($this->extra_macros as $macro => $models) {
            if ( array_key_exists($model, $models) ) {
                $params = (new \ReflectionFunction($this->extra_macros[$macro][$model]))->getParameters();

                $macros[$macro] = [
                    'name' => $macro,
                    'parameters' => $params,
                ];
            }
        }

        return $macros;
    }

    /**
     * @param $name
     *
     * @return static
     */
    private function syncExtraMacros($name): self
    {
        $models = $this->extra_macros[$name];

        Builder::macro($name, function(...$args) use ($name, $models){
            $class = get_class($model = $this->getModel());

            if (! isset($models[$class])) {
                throw new \BadMethodCallException("Call to undefined method ${class}::${name}()");
            }

            $closure = \Closure::bind($models[$class], $model);

            return call_user_func_array($closure, $args);
        });

        return $this;
    }

    /**
     * @param string $model
     *
     * @return static
     */
    private function checkModelSubclass(string $model): self
    {
        if (
            class_exists($model) &&
            (isInstanceOf([$model], \Model::class) || !is_subclass_of($model, \Model::class))
        ) {
            throw new \InvalidArgumentException('$model must be a subclass of ' . \Model::class );
        }

        return $this;
    }

}





//        dE(
//            isInstanceOf($this, \Illuminate\Database\Eloquent\Relations\Relation::class ),
//            func_get_args(),
//            $this
//        );
//        $this->getRelated()
//        $this->getRelationValue()
//        return tap($this->getResults(), function ($results) use ($method) {
//            $this->setRelation($method, $results);
//        });
