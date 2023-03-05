<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Stringable;
use MPhpMaster\LaravelHelpers\mixins\StringableMixin;


try {
    Stringable::mixin(new StringableMixin());
} catch (Exception | ReflectionException $e) {

}
