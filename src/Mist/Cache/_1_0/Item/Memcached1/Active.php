<?php

namespace Mist\Cache\_1_0\Item\Memcached1;

class Active extends \Mist\Cache\_1_0\Item\Memcached1
{
    use \Mist\Cache\_1_0\Item\Store;

    protected function internalGet($key, &$success)
    {
        $value = $this->store->get($key);

        $success = true;
        if ($this->store->getResultCode() === \Memcached::RES_NOTFOUND) {
            $success = false;
            $value   = null;
        }
        return $value;
    }

    protected function internalSet($key, $value, $ttl)
    {
        return $this->store->set($key, $value, (int)$ttl);
    }

    protected function internalDelete($key)
    {
        return $this->store->delete($key);
    }

    protected function internalExists($key)
    {
        $this->store->get($key);
        return $this->store->getResultCode() !== \Memcached::RES_NOTFOUND;
    }
}
