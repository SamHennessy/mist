<?php

namespace Mist\Cache\_1_0\Item\Phalcon1;

class Active implements \Mist\Cache\_1_0\Item
{
    use \Mist\Cache\_1_0\Item\Store;

    protected $error;

    protected function internalGet($key, &$success)
    {
        $value   = $this->store->get($key);
        $success = true;
        if ($value === null) {
            $success = false;
        } else {
            $value = unserialize($value);
        }
        return $value;
    }

    protected function internalSet($key, $value, $ttl)
    {
        $this->error = false;
        try {
            $this->store->save($key, serialize($value), (int)$ttl);
        } catch (\Phalcon\Cache\Exception $exception) {
            $this->error = $exception;
            return false;
        }
        return true;
    }

    protected function internalDelete($key)
    {
        $this->store->delete($key);
        return true;
    }

    protected function internalExists($key)
    {
        return $this->store->exists($key);
    }

    public function getError()
    {
        return $this->error;
    }
}
