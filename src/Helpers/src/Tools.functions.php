<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */
/** @noinspection ForgottenDebugOutputInspection */

use Illuminate\Support\Facades\Route;
use mPhpMaster\Support\Suffixer;
use mPhpMaster\Support\With;

if ( !defined('e') )
    /**
     *
     */
    define('e', 'else');

//class isPlainVar { public function __construct($var="var") { $this->plain = $var; } };
if ( !defined('UNUSED') )
    /**
     *
     */
    define('UNUSED', gzcompress(serialize(['plain' => 0x0011]), 9));
//    define('UNUSED', gzcompress(serialize(new isPlainVar('Variable')), 9));

//dd(UNUSED);
if ( !function_exists('toLocaleDate') ) {

    /**
     * @param $date
     *
     * @return mixed
     */
    function toLocaleDate($date)
    {
        $ar = [
            "الأحد",
            "أح",
            "الإثنين",
            "إث",
            "الثلاثاء",
            "ث",
            "الأربعاء",
            "أر",
            "الخميس",
            "خ",
            "الجمعة",
            "ج",
            "السبت",
            "س",
            "ص",
            "ص",
            "م",
            "م",
            "يناير",
            "يناير",
            "فبراير",
            "فبراير",
            "مارس",
            "مارس",
            "أبريل",
            "أبريل",
            "مايو",
            "مايو",
            "يونيو",
            "يونيو",
            "يوليو",
            "يوليو",
            "أغسطس",
            "أغسطس",
            "سبتمبر",
            "سبتمبر",
            "اكتوبر",
            "اكتوبر",
            "نوفمبر",
            "نوفمبر",
            "ديسمبر",
            "ديسمبر",
        ];
        $notAr = [
            "Sunday",
            "Sun",
            "Monday",
            "Mon",
            "Tuesday",
            "Tue",
            "Wednesday",
            "Wed",
            "Thursday",
            "Thu",
            "Friday",
            "Fri",
            "Saturday",
            "Sat",
            "am",
            "AM",
            "pm",
            "PM",
            "January",
            "Jan",
            "February",
            "Feb",
            "March",
            "Mar",
            "April",
            "Apr",
            "May",
            "May",
            "June",
            "Jun",
            "July",
            "Jul",
            "August",
            "Aug",
            "September",
            "Sep",
            "October",
            "Oct",
            "November",
            "Nov",
            "December",
            "Dec",
        ];

//        $timestamp = strtotime('2019-01-01');
//        $months = [];
//
//        for ($i = 0; $i < 12; $i++) {
//            $months[] = strftime('%B', $timestamp);
//            $months[] = strftime('%b', $timestamp);
//            $timestamp = strtotime('+1 month', $timestamp);
//        }
//        dd($months);


        try {
            if ( !app()->isLocale('ar') || !$date )
                return $date;


            return str_ireplace(
                $notAr,
                $ar,
                $date
            );
        } catch (\Exception $exception) {
            return $date;
        }
    }
}

if ( !function_exists('whenUsed') ) {
    /**
     * @param          $var
     * @param callable $callable
     *
     * @return bool
     */
    function whenUsed($var, callable $callable): bool
    {
        if ( $var !== UNUSED ) {
            $callable($var);
            return true;
        }

        return false;
    }
}

if ( !function_exists('when') ) {
    /**
     * if $condition then call $whenTrue|null else call $whenFalse|null
     *
     * @param bool|mixed    $condition
     * @param callable|null $whenTrue
     * @param callable|null $whenFalse
     * @param mixed|null    $with
     *
     * @return mixed|null
     */
    function when($condition, callable $whenTrue = null, callable $whenFalse = null, $with = null)
    {
        if ( value($condition) ) {
            return is_callable($whenTrue) ? $whenTrue($condition, $with) : $whenTrue;
        } else {
            return is_callable($whenFalse) ? $whenFalse($condition, $with) : $whenFalse;
        }
    }
}

if ( !function_exists('whenEmpty') ) {
    /**
     * Apply the callback if the collection is empty.
     *
     * @param \Illuminate\Support\Collection|array|mixed $collection
     * @param callable|null                              $empty
     * @param callable|null                              $notEmpty
     *
     * @return mixed
     */
    function whenEmpty($collection, callable $empty, callable $notEmpty = null)
    {
        return toCollectWithModel($collection)->pipe(function ($_collection) use ($collection, $empty, $notEmpty) {
            if ( $collection instanceof \Illuminate\Database\Eloquent\Model ) {
                if ( empty($collection->getAttributes()) ) {
                    return $empty($collection, true);
                } else {
                    return $notEmpty($collection, false);
                }
            }

            return $_collection->when($_collection->isEmpty(), $empty, $notEmpty);
        });
    }
}

if ( !function_exists('whenNotEmpty') ) {
    /**
     * Apply the callback if the collection is not empty.
     *
     * @param \Illuminate\Support\Collection|array|mixed $collection
     * @param callable|null                              $empty
     * @param callable|null                              $notEmpty
     *
     * @return mixed
     */
    function whenNotEmpty($collection, callable $notEmpty, callable $empty = null)
    {
        return toCollectWithModel($collection)->pipe(function ($_collection) use ($collection, $notEmpty, $empty) {
            if ( $collection instanceof \Illuminate\Database\Eloquent\Model ) {
                if ( !empty($collection->getAttributes()) ) {
                    return $notEmpty($collection, true);
                } else {
                    return $empty($collection, false);
                }
            }

            return $_collection->when(!$_collection->isEmpty(), $notEmpty, $empty);
        });
    }
}

if ( !function_exists('makeWith') ) {
    /**
     * @param mixed ...$value
     *
     * @return With
     */
    function makeWith(...$value)
    {
        return new With($value);
    }
}

// region: return
if ( !function_exists('returnCallable') ) {
    /**
     * Determine if the given value is callable, but not a string.
     *
     *
     * **Source**: ---  {@link \Illuminate\Support\Collection Laravel Collection}
     *
     * @param mixed $value
     *
     * @return \Closure
     */
    function returnCallable($value): \Closure
    {
        if ( !is_callable($value) ) {
            return returnClosure($value);
        }

        if ( is_string($value) ) {
            return Closure::fromCallable($value);
        }

        return $value;
    }
}

if ( !function_exists('returnClosure') ) {
    /**
     * Returns function that returns any arguments u sent;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnClosure(...$data)
    {
        $_data = head($data);
        if ( func_num_args() > 1 ) {
            $_data = $data;
        } else if ( func_num_args() === 0 ) {
            $_data = returnNull();
        }

        return function () use ($_data) {
            return value($_data);
        };
    }
}

if ( !function_exists('returnArray') ) {
    /**
     * Returns function that returns [];
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnArray(...$data)
    {
        return returnClosure($data);
    }
}

if ( !function_exists('returnCollect') ) {
    /**
     * Returns function that returns Collection;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnCollect(...$data)
    {
        return function (...$args) use ($data) {
            return collect($data)->merge($args);
        };
    }
}

if ( !function_exists('returnArgs') ) {
    /**
     * Returns function that returns func_get_args();
     *
     * @return \Closure
     */
    function returnArgs()
    {
        return function () {
            return func_get_args();
        };
    }
}

if ( !function_exists('returnThis') ) {
    /**
     * Returns function that returns $this;
     *
     * @return \Closure
     */
    function returnThis()
    {
        return returnClosureBinder(function () {
            return $this;
        }, ...func_get_args());
    }
}

if ( !function_exists('returnStaticName') ) {
    /**
     * Returns function that returns static::class
     *
     * @return \Closure
     */
    function returnStaticName()
    {
        return returnClosureBinder(function () {
            /** @noinspection PhpUndefinedClassInspection */
            return static::class;
        }, ...func_get_args());
    }
}

if ( !function_exists('returnClosureBinder') ) {
    /**
     * Returns function that accepts Closure and scope and returns new Closure.
     *
     * @param \Closure $closure The anonymous function to bind
     *
     * @return \Closure
     */
    function returnClosureBinder($closure = null)
    {
        if ( isClosure($closure) ) {
            /**
             * @param object|null $newthis The object to which the given anonymous function should be bound, or NULL for the closure to be unbound.
             * @param mixed       $newscope The class scope to which associate the closure is to be associated, or 'static' to keep the current one.
             * If an object is given, the type of the object will be used instead.
             * This determines the visibility of protected and private methods of the bound object.
             *
             * @return Closure Returns the newly created Closure object
             */
            $return = function ($newthis = null, $newscope = 'static') use (&$closure) {
                return $newthis ? bindTo($closure, $newthis, $newscope) : $closure;
            };
        } else {
            /**
             * @param object|null $newthis The object to which the given anonymous function should be bound, or NULL for the closure to be unbound.
             * @param mixed       $newscope The class scope to which associate the closure is to be associated, or 'static' to keep the current one.
             * If an object is given, the type of the object will be used instead.
             * This determines the visibility of protected and private methods of the bound object.
             *
             * @return Closure Returns the newly created Closure object
             */
            $return = function (\Closure $_closure = null, $newthis = null, $newscope = 'static') {
                return $newthis ? bindTo($_closure, $newthis, $newscope) : $_closure;
            };
        }

        if ( func_num_args() > 1 ) {
            $args = func_get_args();
            array_shift($args);
            return $return(...$args);
        }

        return $return;
    }
}

if ( !function_exists('returnString') ) {
    /**
     * Returns function that returns ""
     *
     * @param string|null $text
     *
     * @return \Closure
     */
    function returnString(?string $text = "")
    {
        return returnClosure((string)$text);
    }
}

if ( !function_exists('returnNull') ) {
    /**
     * Returns function that returns null;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnNull()
    {
        return function () {
            return null;
        };
    }
}

if ( !function_exists('returnTrue') ) {
    /**
     * Returns function that returns true;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnTrue()
    {
        return returnClosure(true);
    }
}

if ( !function_exists('returnFalse') ) {
    /**
     * Returns function that returns false;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnFalse()
    {
        return returnClosure(false);
    }
}
// endregion: return

#region IS
if ( !function_exists('isUsed') ) {
    /**
     * @param mixed $var
     *
     * @return bool
     */
    function isUsed($var): bool
    {
        return $var !== UNUSED;
    }
}

if ( !function_exists('isUnused') ) {
    /**
     * @param mixed $var
     *
     * @return bool
     */
    function isUnused($var): bool
    {
        return $var === UNUSED;
    }
}

if ( !function_exists('ifSet') ) {
    /**
     * @param mixed $var
     * @param string|mixed $true
     * @param string|mixed $false
     *
     * @return bool|string|null
     */
    function ifSet($var, $true = UNUSED, $false = UNUSED)
    {
        isUnused($true) && ($true = $var ?? returnTrue());
        isUnused($false) && ($false = returnNull());

        return value(isset($var) ? $true : $false);
    }
}

if ( !function_exists('firstSet') ) {
    /**
     * @param mixed ...$var
     *
     * @return mixed|null
     */
    function firstSet(...$var)
    {
        foreach ($var as $_var)
            if ( isset($_var) )
                return $_var;

        return null;
    }
}

if ( !function_exists('getAny') ) {
    /**
     * @param mixed ...$vars
     *
     * @return mixed|null
     */
    function getAny(...$vars)
    {
        foreach ($vars as $_var) {
            if ( $_var ) {
                return $_var;
            }
        }

        return null;
    }
}

if ( !function_exists('test') ) {
    /**
     * Apply `value` function to each argument. when value returns something true ? return it.
     *
     * @param mixed ...$vars
     *
     * @return mixed|null
     */
    function test(...$vars)
    {
        foreach ($vars as $_var)
            if ( $_var = value($_var) ) {
                return $_var;
            }

        return null;
    }
}

if ( !function_exists('iif') ) {
    /**
     * Test Condition and return one of two parameters
     *
     * @param mixed $var Condition
     * @param mixed $true Return this if Condition == true
     * @param mixed $false Return this when Condition fail
     *
     * @return mixed
     */
    function iif($var, $true = null, $false = null)
    {
        return value($var ? $true : $false);
    }
}
#endregion

#region HAS
if ( !function_exists('hasTrait') ) {
    /**
     * Check if given class has trait.
     *
     * @param mixed  $class <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $traitName <p>
     *                          Trait name to check
     *                          </p>
     *
     * @return bool
     */
    function hasTrait($class, $traitName)
    {
        try {
            $traitName = str_contains($traitName, "\\") ? class_basename($traitName) : $traitName;

            $hasTraitRC = new ReflectionClass($class);
            $hasTrait = collect($hasTraitRC->getTraitNames())->map(function ($name) use ($traitName) {
                    $name = str_contains($name, "\\") ? class_basename($name) : $name;

                    return $name == $traitName;
                })->filter()->count() > 0;
        } catch (ReflectionException $exception) {
            $hasTrait = false;
        } catch (Exception $exception) {
            d($exception->getMessage());
            $hasTrait = false;
        }

        return $hasTrait;
    }
}

if ( !function_exists('hasKey') ) {
    /**
     * Check if given array has key if has key call $callable.
     *
     * @param array        $array
     * @param string       $key
     * @param Closure|null $callable
     *
     * @return bool|mixed
     */
    function hasKey($array, $key, Closure $callable = null)
    {
        try {
            $has = array_key_exists($key, $array);
            if ( $callable && is_callable($callable) ) {
                return $callable->call($array, $array);
            }

            return $has === true;
        } catch (Exception $exception) {
            d($exception->getMessage());
        }

        return false;
    }
}

if ( !function_exists('hasScope') ) {
    /**
     * Check if given class has the given scope name.
     *
     * @param mixed  $class <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $scopeName <p>
     *                          Scope name to check
     *                          </p>
     *
     * @return bool
     */
    function hasScope($class, $scopeName)
    {
        try {
            $hasScopeRC = new ReflectionClass($class);
            $scopeName = strtolower(studly_case($scopeName));
            starts_with($scopeName, "scope") && ($scopeName = substr($scopeName, strlen("scope")));

            $hasScope = collect($hasScopeRC->getMethods())->map(function ($c) use ($scopeName) {
                    /**
                     * @var $c ReflectionMethod
                     */
                    $name = strtolower(studly_case($c->getName()));
                    $name = starts_with($name, "scope") ? substr($name, strlen("scope")) : false;

                    return $name == $scopeName;
                })->filter()->count() > 0;
        } catch (ReflectionException $exception) {
            $hasScope = false;
        } catch (Exception $exception) {
            $hasScope = false;
        }

        return !!$hasScope;
    }
}

if ( !function_exists('hasConst') ) {
    /**
     * Check if given class has the given const.
     *
     * @param mixed  $class <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $const <p>
     *                          Const name to check
     *                          </p>
     *
     * @return bool
     */
    function hasConst($class, $const): bool
    {
        $hasScope = false;
        try {
            if ( is_object($class) || is_string($class) ) {
                $reflect = new ReflectionClass($class);
                $hasScope = array_key_exists($const, $reflect->getConstants());
            }
        } catch (ReflectionException $exception) {
            $hasScope = false;
        } catch (Exception $exception) {
            $hasScope = false;
        }

        return (bool)$hasScope;
    }
}
#endregion

#region GET
if ( !function_exists('suffixerMaker') ) {
    /**
     * Alias for: {@link Suffixer::makeer}
     *
     * @return Closure
     */
    function suffixerMaker(): Closure
    {
        return Suffixer::makeer(...func_get_args());
    }
}
if ( !function_exists('str_prefix') ) {
    /**
     * Add a prefix to string but only if string2 is not empty.
     *
     * @param string      $string string to prefix
     * @param string      $prefix prefix
     * @param string|null $string2 string2 to prefix the return
     *
     * @return string|null
     */
    function str_prefix($string, $prefix, $string2 = null)
    {
        $newString = rtrim(is_null($string2) ? '' : $string2, $prefix) .
            $prefix .
            ltrim($string, $prefix);

        return ltrim($newString, $prefix);
    }
}

if ( !function_exists('str_suffix') ) {
    /**
     * Add a suffix to string but only if string2 is not empty.
     *
     * @param string      $string string to suffix
     * @param string      $suffix suffix
     * @param string|null $string2 string2 to suffix the return
     *
     * @return string|null
     */
    function str_suffix($string, $suffix, $string2 = null)
    {
        $newString = ltrim($string, $suffix) . $suffix . rtrim(is_null($string2) ? '' : $string2, $suffix);

        return trim($newString, $suffix);
    }
}

if ( !function_exists('str_words_limit') ) {
    /**
     * Limit string words.
     *
     * @param string      $string string to limit
     * @param int         $limit word limit
     * @param string|null $suffix suffix the string
     *
     * @return string
     */
    function str_words_limit($string, $limit, $suffix = '...')
    {
        $start = 0;
        $stripped_string = strip_tags($string); // if there are HTML or PHP tags
        $string_array = explode(' ', $stripped_string);
        $truncated_array = array_splice($string_array, $start, $limit);

        $lastWord = end($truncated_array);
        $return = substr($string, 0, stripos($string, $lastWord) + strlen($lastWord)) . ' ' . $suffix;

        $m = [];
        if ( preg_match_all('#<(\w+).+?#is', $return, $m) ) {
            $m = is_array($m) && is_array($m[1]) ? array_reverse($m[1]) : [];
            foreach ($m as $HTMLTAG) {
                $return .= "</{$HTMLTAG}>";
            }
        }

        return $return;
    }
}

if ( !function_exists('basenameOf') ) {
    /**
     * Returns basename of the given string after replace slashes and back slashes to DIRECTORY_SEPARATOR
     *
     * @param string $string
     *
     * @return string
     */
    function basenameOf(string $string)
    {
        $string = replaceAll([
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        ], $string);

        return basename($string);
    }
}
#endregion
