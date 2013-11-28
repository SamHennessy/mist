<?php

namespace MistTest\Cache\_1_0\Pool\Memcached1;

trait Base
{
    use \MistTest\Cache\_1_0\Pool\Base;

    protected $memcached;

    protected $pool;

    abstract protected function createPool();

    protected function setUp()
    {
        $this->memcached = new \Memcached();
        $this->memcached->addServer('localhost', 11211);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        foreach ($this->usedKeyList as $key) {
            $this->internalDelete($key);
        }
    }

    protected function internalSet($key, $value)
    {
        $this->memcached->set($key, $value, 5);
    }

    protected function internalDelete($key)
    {
        if (@$this->assertCacheKeyBase($key)) {
            $this->memcached->delete($key);
        }
    }
}
