<?php

namespace Mist\Cache\_1_0\Item\Memcached1;

class Passive implements \Mist\Cache\_1_0\Item
{
    use \Mist\Cache\_1_0\Item\Validation;

    protected $pool;

    protected $key;

    protected $value;

    protected $isHit;

    public function __construct(\Mist\Cache\_1_0\Pool $pool, $key, $value, $isHit)
    {
        if ($this->assertCacheKeyBase($key) === false)
        {
            throw new \Mist\Cache\_1_0\Item\InvalidArgumentException(
                'Passed key not valid',
                \Mist\Cache\_1_0\Item\Exception::CACHE_1_0_INVALID_KEY
            );
        }

        $this->pool  = $pool;
        $this->key   = $key;
        $this->value = $value;
        $this->isHit = $isHit;
    }

    public function getKey()
    {
        return $this->key;
    }


    public function get()
    {
        return $this->value;
    }


    public function set($value = null, $ttl = null)
    {
        $item        = $this->pool->getItem($this->key);
        $success     = $item->set($value, $ttl);
        $this->value = $item->get();
        $this->isHit = $item->isHit();
        return $success;
    }


    public function isHit()
    {
        return $this->isHit;
    }


    public function delete()
    {
        $this->pool->getItem($this->key)->delete();
        // We'll have to assume success
        $this->isHit = false;
        $this->value = null;

        return $this;
    }


    public function exists()
    {
        return $this->pool->getItem($this->key)->exists();
    }
}
