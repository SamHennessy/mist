<?php

namespace Mist\Cache\_1_0\Pool;

abstract class Memcached1 implements \Mist\Cache\_1_0\Pool
{
    protected $store;

    public function __construct(\Memcached $memcached)
    {
        $this->store = $memcached;
    }
    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return an ItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     * @return \Psr\Cache\ItemInterface
     *   The corresponding Cache Item.
     * @throws \Psr\Cache\InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     */
    public function getItem($key)
    {
        return new \Mist\Cache\_1_0\Item\Memcached1\Active($this->store, $key);
    }

    /**
     * Deletes all items in the pool.
     *
     * @return \Psr\Cache\PoolInterface
     *   The current pool.
     */
    public function clear()
    {
        $this->store->flush();
        return $this;
    }
}
