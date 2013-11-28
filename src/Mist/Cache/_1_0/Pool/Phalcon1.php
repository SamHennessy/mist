<?php

namespace Mist\Cache\_1_0\Pool;

class Phalcon1 implements \Mist\Cache\_1_0\Pool
{
    protected $store;

    public function __construct($store)
    {
        $this->store = $store;
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
        return new \Mist\Cache\_1_0\Item\Phalcon1\Active($this->store, $key);
    }

    /**
     * Deletes all items in the pool.
     *
     * Not supported by Phalcon
     *
     * @return \Psr\Cache\PoolInterface
     *   The current pool.
     */
    public function clear()
    {
        return $this;
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys
     *   An indexed array of keys of items to retrieve.
     * @return \Traversable
     *   A traversable collection of Cache Items in the same order as the $keys
     *   parameter, keyed by the cache keys of each item. If no items are found
     *   an empty Traversable collection will be returned.
     */
    public function getItems(array $keys)
    {
        do
        {
            $key   = current($keys);
            $value = $this->store->get($key);
            if ($value !== null) {
                yield $key => new \Mist\Cache\_1_0\Item\Passive(
                    $this,
                    $key,
                    unserialize($value),
                    true
                );
            }
        } while (next($keys));
    }
}
