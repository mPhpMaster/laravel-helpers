<?php
/*
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

//if ( !function_exists('') ) {
//    /***/
//    function ()
//    {
//        return;
//    }
//}

if ( !function_exists('grep') ) {
    /**
     * @param $data
     * @param $grep
     *
     * @return array
     */
    function grep($data, $grep)
    {
        $is = is_array($data) ? $data : [$data];
        return mapEach($is, function ($value) use ($grep) {
            if ( StringContains($value, $grep) ) {
                return $value;
            }
            return null;
        });

    }
}

if ( !function_exists('getInterfaces') ) {
    /**
     * @param string|string[]|null $grep
     *
     * @return array
     */
    function getInterfaces($grep = null)
    {
        $result = array_values(get_declared_interfaces());
        if ( !is_null($grep) ) {
            $result = filterEach($result,$grep, false);
        }

        return $result;
    }
}

if ( !function_exists('getClasses') ) {
    /**
     * @param string|string[]|null $grep
     *
     * @return array
     */
    function getClasses($grep = null)
    {
        $result = array_values(get_declared_classes());
        if ( !is_null($grep) ) {
            $result = filterEach($result,$grep, false);
        }

        return $result;
    }
}

if ( !function_exists('getTraits') ) {
    /**
     * @param string|string[]|null $grep
     *
     * @return array
     */
    function getTraits($grep = null)
    {
        $result = array_values(get_declared_traits());
        if ( !is_null($grep) ) {
            $result = filterEach($result,$grep, false);
        }

        return $result;
    }
}

if ( !function_exists('getAllDeclared') ) {
    /**
     * @param string|string[]|null $grep
     *
     * @return array
     */
    function getAllDeclared($grep = null)
    {
        $result = array_merge(getClasses(), getInterfaces(), getTraits());
        if ( !is_null($grep) ) {
            $result = filterEach($result,$grep, false);
        }

        return array_values($result);
    }
}

if ( !function_exists('getModelAbstractClass') ) {
    /**
     * @param object|string|null $test_class
     *
     * @return string|bool
     * @todo: change the return to model parent class
     */
    function getModelAbstractClass($test_class = null)
    {
        if ( $test_class ) {
            $test_class = is_object($test_class) ? $test_class : app(getRealClassName($test_class));

            $test_abstract_class = getModelAbstractClass();
            return $test_class instanceof $test_abstract_class;
        }

        return \App\Models\AppModel::class;
    }
}

// region: data loop
if ( !function_exists('dataForEach') ) {
    /**
     * @param array|mixed $array
     * @param callable    $callback
     * @param bool        $map
     *
     * @return array
     */
    function dataForEach($array, callable $callback, $map = true)
    {
        $result = [];

        foreach ((array)$array as $key => $value) {
            $crnt_data = ['value' => &$value, 'key' => &$key, 'all' => &$array/*, 'result' => &$result*/];

            try {
                $return = call_user_func_array($callback, [&$value, &$key, &$array, &$crnt_data]);

                if ( $map ) {
                    if ( isClosure($map) ) {
                        call_user_func_array($map, [
                            &$return,
                            $put = function ($newValue = null, $newKey = null) use (&$result, &$return, &$key, &$value) {
                                if ( func_num_args() === 0 ) {
                                    $result[ $key ] = $value;
                                } elseif ( func_num_args() === 1 ) {
                                    $result[ $key ] = $newValue;
                                } elseif ( func_num_args() === 2 ) {
                                    $result[ $newKey ] = $newValue;
                                }

                                return $result;
                            },
                            $skip = function () use (&$result) {
                                return $result;
                            },
                            &$crnt_data
                        ]);
                    } else if ( !is_null($return) ) {
                        $result[ $key ] = $return;
                    }
                }
            } catch (Exception $exception) {
                break;
            }
        }

        return $map ? $result : $array;
    }
}

if ( !function_exists('applyEach') ) {
    /**
     * @param array|mixed $array
     * @param callable    $callback
     *
     * @return array
     */
    function applyEach($array, callable $callback)
    {
        return dataForEach($array, $callback, false);
    }
}

if ( !function_exists('mapEach') ) {
    /**
     * @param array|mixed $array
     * @param callable    $callback
     *
     * @return array
     */
    function mapEach($array, callable $callback)
    {
        return dataForEach($array, $callback, true);
    }
}

if ( !function_exists('filterEach') ) {
    /**
     * @param array|mixed $array
     * @param callable    $callback
     *
     * @param bool        $strict
     *
     * @return array
     */
    function filterEach($array, $for = null, $strict = false)
    {
        return dataForEach($array, function ($v) use ($for, $strict) {
            return StringContains($v, $for) !== false ||
                ($strict === false && StringContains(snake_case($v), mapEach($for, fromCallable('snake_case'))) !== false);
        }, function ($returns, $put, $skip, $data) use ($strict) {
            $pass = $strict ? $returns !== false : !!$returns;
            ($pass && $put($data['value'])) || $skip;
        });
    }
}
// endregion: data loop
