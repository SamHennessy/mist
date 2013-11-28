<?php

namespace MistTest\Cache\_1_0\Item\Zend2;

/**
 * The following test is written is such away that assume that memory caching
 * will be layered on top of internal cache storage. As such this is not a
 * true unit test. It requires access to the internal system also.
 * 
 */
class ActiveTest extends \MistTest\Cache\_1_0\Item\ActiveTest
{
    use \Mist\Cache\_1_0\Item\Validation;

    protected $store;

    protected $storeMock;

    protected $vfsRoot;

    protected function setUp()
    {
        $this->vfsRoot = \org\bovigo\vfs\vfsStream::setup();

        $this->store = \Zend\Cache\StorageFactory::factory(
            array('adapter' => array('name' => 'memory'))
        );

        $this->storeMock =
            $this->getMock(\Zend\Cache\Storage\Adapter\Memory::class);

        // Hack to allow use of vfs, keeping around for reference
        // $options     = $this->store->getOptions();
        // $refObject   = new \ReflectionObject($options);
        // $refProperty = $refObject->getProperty('cacheDir');
        // $refProperty->setAccessible(true);
        // $refProperty
        //     ->setValue($options, \org\bovigo\vfs\vfsStream::url('root'));
    }

    protected function internalSet($key, $value)
    {
        $this->store->setItem($key, serialize($value));
    }

    protected function internalGet($key)
    {
        return unserialize($this->store->getItem($key));
    }

    protected function internalHas($key)
    {
        return $this->store->hasItem($key);
    }

    protected function internalDelete($key)
    {
        if (@$this->assertCacheKeyBase($key)) {
            $this->store->removeItem($key);
        }
    }

    protected function createItem($key)
    {
        return new \Mist\Cache\_1_0\Item\Zend2\Active($this->store, $key);
    }

    protected function createItemMockStore($key)
    {
        return new \Mist\Cache\_1_0\Item\Zend2\Active($this->storeMock, $key);
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
        $this->markTestSkipped('Zend 2 cache doesn\'t support per item TTL');
    }

    public function testTtlException()
    {
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);

        $this->setExpectedException(
            \Mist\Cache\_1_0\Item\Exception::class,
            '',
            \Mist\Cache\_1_0\Item\Exception::CACHE_1_0_ZEND_TTL_1);

        $item->set($value, rand(0, 9));
    }

    /**
     * @group unsupported
     */
    public function testTtlNone()
    {
        $this->testTtlException();
    }

    /**
     * @group unsupported
     */
    public function testTtlNull()
    {
        $this->testTtlException();
    }

    /**
     * @group unsupported
     */
    public function testTtlSeconds()
    {
        $this->testTtlException();
    }

    /**
     * @group unsupported
     */
    public function testTtlZero()
    {
        $this->testTtlException();
    }

    /**
     * @group unsupported
     */
    public function testTtlDatetime()
    {
        $this->testTtlException();
    }
}
