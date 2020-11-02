<?php

if ( !interface_exists('hasToString') ) {
    /**
     * Interface hasToString
     */
    interface hasToString
    {
        public function toString(): string;
    }
}
