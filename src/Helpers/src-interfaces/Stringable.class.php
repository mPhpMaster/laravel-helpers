<?php
/*
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
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

