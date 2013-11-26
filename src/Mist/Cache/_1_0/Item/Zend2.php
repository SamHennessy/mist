<?php

namespace Mist\Cache\_1_0\Item;

class Zend2 implements \Mist\Cache\_1_0\Item
{
    use Store;

    protected function internalGet($key, &$success)
    {
        $success = false;
        $value   = $this->store->getItem($key, $success);
        if ($success) {
            $value = unserialize($value);
        }
        return $value;
    }

    protected function internalSet($key, $value, $ttl)
    {
        // TTL in Zend is global
        if ($ttl !== null) {
            throw new Exception(
                'TTL not supported by this adapter',
                Exception::CACHE_1_0_ZEND_TTL_1
            );
        }

        return $this->store->setItem($key, serialize($value));
    }

    protected function internalDelete($key)
    {
        return $this->store->removeItem($key);
    }

    protected function internalExists($key)
    {
        return $this->store->hasItem($key);
    }
}
