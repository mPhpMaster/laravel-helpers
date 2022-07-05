<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if ( !interface_exists('hasToString') ) {
    /**
     * Interface hasToString
     */
    interface hasToString
    {
        public function toString(): string;
    }
}
