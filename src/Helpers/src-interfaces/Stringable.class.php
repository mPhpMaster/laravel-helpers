<?php
/**
 * Copyright آ© 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if ( !interface_exists('Stringable') && !interface_exists('Illuminate\Contracts\Support\Stringable') ) {
    /**
     * Interface Stringable
     */
    interface Stringable
    {
        /**
         * @return string
         */
        public function __toString();
    }
} else if(interface_exists('Illuminate\Contracts\Support\Stringable')) {
    class_alias('Illuminate\Contracts\Support\Stringable', 'Stringable');
}

