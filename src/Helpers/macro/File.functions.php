<?php
/**
 * Copyright © 2020 mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Collection;
use mPhpMaster\Support\mixins\CollectionsMixin;


try {
    \Illuminate\Support\Str::mixin(new CollectionsMixin());
} catch (Exception | ReflectionException $e) {

}
