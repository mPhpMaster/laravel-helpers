<?php

if ( !interface_exists('Arrayable') && !interface_exists('Illuminate\Contracts\Support\Arrayable') ) {
    /**
     * Interface Arrayable
     */
    interface Arrayable
    {
        /**
         * Get the instance as an array.
         *
         * @return array
         */
        public function toArray();
    }
} else if(interface_exists('Illuminate\Contracts\Support\Arrayable')) {
    class_alias('Illuminate\Contracts\Support\Arrayable', 'Arrayable');
}

