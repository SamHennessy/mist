<?php

namespace MistTest\Cache\_1_0\Item\Memcached1;

class ActiveTest extends \MistTest\Cache\_1_0\Item\ActiveTest
{
    use \Mist\Cache\_1_0\Item\Validation;

    protected $store;

    protected function internalSet($key, $value)
    {
        $this->store->set($key, $value, 5);
    }

    protected function internalGet($key)
    {
        return $this->store->get($key);
    }

    protected function internalHas($key)
    {
        $this->store->get($key);
        return $this->store->getResultCode() !== \Memcached::RES_NOTFOUND;
    }

    protected function internalDelete($key)
    {
        if (@$this->assertCacheKeyBase($key)) {
            $this->store->delete($key);
        }
    }

    protected function setUp()
    {
        if (class_exists(\Memcached::class) === false) {
            $this->markTestSkipped('Memcached not available');
        }

        $this->store = new \Memcached();
        $this->store->addServer('localhost', 11211);

        $this->storeMock = $this->getMock(\Memcached::class);
    }

    protected function createItem($key)
    {
        return new \Mist\Cache\_1_0\Item\Memcached1\Active($this->store, $key);
    }

    protected function createItemMockStore($key)
    {
        return new \Mist\Cache\_1_0\Item\Memcached1\Active($this->storeMock, $key);
    }

    /**
     * @var mixed $key
     *   - Doesn't need to be tested
     * @var mixed $value
     *   - Doesn't need to be tested
     * @var mixed $ttl
     *   - Number of seconds to time from "now".
     *   - This value will need to be converted to what is going to be passed
     *     to the store.
     */
    protected function setTtlExpectation($key, $value, $ttlSeconds, $ttlRaw)
    {
        $this->storeMock
            // One call ...
            ->expects($this->once())
            // to the set method. ...
            ->method('set')
            // With ...
            ->with(
                // any key, ...
                $this->anything(),
                // any value, ...
                $this->anything(),
                // and converted (if needed) TTL
                $this->equalTo($ttlSeconds)
            );
    }
}
