<?php

namespace Mist\Cache\_1_0\Item;

trait Validation
{
    public function assertCacheKeyBase($key)
    {
        $a[] = assert('is_string($key)', '$key must be a string');
        $a[] = assert(
            'preg_match("/[\{\}\(\)\/\\\\\\\\@:]/", $key) === 0',
            'Keys may not contain any of the following "{}()/\@:"'
        );

        if (in_array(false, $a)) {
            return false;
        }
        return true;
    }
}
