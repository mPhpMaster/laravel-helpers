<?php
/*
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
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
} else if(interface_exists('Illuminate\Contracts\Support\Jsonable')) {
    class_alias('Illuminate\Contracts\Support\Jsonable', 'Jsonable');
}
