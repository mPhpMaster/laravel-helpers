<?php

namespace mPhpMaster\Support\Traits;

/**
 * Trait THasAttributesWithString
 *
 * @mixin \mPhpMaster\Support\Interfaces\IHasAttributesWithString
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @package mPhpMaster\Support\Traits
 */
trait THasAttributesWithString
{
    /**
     * **Override**
     *
     * $this->{any}_with_{any}
     *
     * @param $key
     *
     * @return array|null|string
     */
    public function getAttribute($key)
    {
        $newKey = snake_case($key);
        // no overrides
        if (
            array_key_exists($key, $this->attributes) ||
            array_key_exists($newKey, $this->attributes) ||
            $this->hasGetMutator($key) ||
            $this->hasGetMutator($newKey)
        ) return parent::getAttribute($key);

        if ( ($stringInfo = static::getStringInfoFromAttributeName($newKey)) && $stringInfo['allowed'] ) {
            $newKey = static::getAttributeNameWithoutString($newKey);
            if ( array_key_exists($newKey, $this->attributes) || $this->hasGetMutator($newKey) ) {
                return $this->suffixWithString($this->getAttribute($newKey), $stringInfo['string_name']);
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * Returns attribute name without _with_*.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getAttributeNameWithoutString($name): string
    {
        return str_before($name, '_with_');
    }

    /**
     * Returns method that apply the suffixing.
     *
     * @return \Closure
     */
    public static function getDefaultStringSuffixer(): \Closure
    {
        return function ($value, $string, $model) {
            return $value . ((empty(trim($value)) && trim($value) != '0') ? '' : " {$string}");
        };
    }

    /**
     * $this->with_string
     *
     * @param string|null $string
     * @param string|null $value
     *
     * @return object|string|mixed
     */
    public function getWithStringAttribute($value = null)
    {
        return $this->withString(null, $value);
    }

    /**
     * $this->with_string
     *
     * @param string|null $string
     * @param string|null $value
     *
     * @return object|string|mixed
     */
    public function withString($string = null, $value = null) {
        if ( func_num_args() > 1 ) {
            return $this->suffixWithString($value, $string);
        }

        return new class ($this, $string) {
            /** @var THasAttributesWithString $class */
            protected $class;
            /** @var string $string */
            public $string;

            /**
             *  constructor.
             *
             * @param $class
             * @param $string
             */
            public function __construct($class, $string)
            {
                $this->class = $class;
                $this->string = $string;
            }

            public function string($string = null)
            {
                $this->string = $string;
                return $this;
            }

            public function __get($name)
            {
                return $this->class->suffixWithString($this->class->{$name}, $this->string);
            }

            /**
             * @param $name
             * @param $pars
             *
             * @return string
             */
            public function __call($name, $pars)
            {
                return $this->class->suffixWithString($this->class->{$name}, head($pars) ?: $this->string);
            }
        };
    }

    /**
     * Apply the string suffix on value by formatter.
     *
     * @param string|null   $value
     * @param string        $stringName
     * @param callable|null $formatter
     *
     * @return string
     */
    public function suffixWithString($value, $stringName, callable $formatter = null): string
    {
        if(is_null($stringName)) return '';

        $stringValue = static::getStringByName($stringName);

        if ( $stringValue instanceof \Closure ) {
            $stringValue = $stringValue($this);
        }

        $formatter = is_callable($formatter) ? $formatter : static::getDefaultStringSuffixer();

        return $formatter($value, $stringValue, $this);
    }

    /**
     * Returns string name by value.
     *
     * @param string $value
     *
     * @return string|mixed
     */
    public static function getStringNameByValue(string $value)
    {
        return collect(static::getAllowedStrings())->search(function ($v) use ($value) {
            return $v == $value;
        });
    }

    /**
     * Returns string value by name.
     *
     * @param string $name
     *
     * @return mixed|string|null
     */
    public static function getStringByName(string $name)
    {
        return static::getStringInfoFromAttributeName($name)['string_value'];
    }

    /**
     * Returns string value by name.
     *
     * @param string $attribute_name
     *
     * @return array
     */
    public static function getStringInfoFromAttributeName(string $attribute_name): array
    {
        return [
            'string_name' => $string_name = str_after($attribute_name, '_with_'),
            'string_value' => ($string_value = collect(static::getAllowedStrings())->get($string_name, returnClosure(''))),
            'attribute_name' => str_before($attribute_name, '_with_'),
            'allowed' => !is_null($string_value)
        ];
    }

    /**
     * Returns all allowed strings name & value.
     *
     * @return array [ name => string ]
     */
    abstract public static function getAllowedStrings(): array;

}
