<?php

namespace Mist\Cache\_1_0\Pool\Memcached1;

class Safe extends \Mist\Cache\_1_0\Pool\Memcached1
{
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
        $this->store->getDelayed($keys);

        $list = $this->store->fetchAll();

        if ($list === false) {
            return;
        }

        do
        {
            $i = key($list);
            yield $list[$i]['key'] => new \Mist\Cache\_1_0\Item\Passive(
                $this,
                $list[$i]['key'],
                $list[$i]['value'],
                true
            );
        } while (next($list));

    }
}
