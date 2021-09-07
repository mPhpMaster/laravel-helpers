<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if ( !interface_exists('Jsonable') && !interface_exists('Illuminate\Contracts\Support\Jsonable') ) {
    /**
     * Interface Jsonable
     */
    interface Jsonable
    {
        /**
         * Convert the object to its JSON representation.
         *
         * @param  int  $options
         * @return string
         */
        public function toJson($options = 0);
    }
}

if(interface_exists('Illuminate\Contracts\Support\Jsonable')) {
    class_alias('Illuminate\Contracts\Support\Jsonable', 'Jsonable');
}
