<?php

namespace Mist\Cache\_1_0\Pool;

class Zend2 implements \Mist\Cache\_1_0\Pool
{
    use Building;

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
        $this->getPool();
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
        $this->getPool();
    }

    /**
     * Deletes all items in the pool.
     *
     * @return \Psr\Cache\PoolInterface
     *   The current pool.
     */
    public function clear()
    {
        $this->getPool();
    }

    protected function validateBuilingPool($builtObject)
    {
        return $builtObject instanceof \Zend\Cache\Storage\StorageInterface;
    }

    protected function validateBuilingItem($builtObject)
    {
        return false;
    }
}
