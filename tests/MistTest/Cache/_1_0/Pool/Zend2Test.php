<?php

namespace MistTest\Cache\_1_0\Pool;

class Zend2Test extends \PHPUnit_Framework_TestCase
{
    use Base;

    protected $store;

    protected $pool;

    protected function setUp()
    {
        $this->store = \Zend\Cache\StorageFactory::factory(
            array('adapter' => array('name' => 'Memory'))
        );
    }

    protected function createPool()
    {
        return new \Mist\Cache\_1_0\Pool\Zend2($this->store);
    }

    protected function internalSet($key, $value)
    {
        $this->store->setItem($key, serialize($value));
    }

    protected function internalDelete($key)
    {
        if (@$this->assertCacheKeyBase($key)) {
            $this->store->removeItem($key);
        }
    }
}
