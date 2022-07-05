<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

/**
 * Trait THasToString
 *
 * @mixin Stringable
 */
trait THasToString {

    public function toString(): string {
        return (string)$this->__toString();
    }
}
