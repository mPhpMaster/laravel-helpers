<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

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
