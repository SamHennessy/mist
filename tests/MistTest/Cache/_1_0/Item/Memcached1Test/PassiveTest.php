<?php

namespace MistTest\Cache\_1_0\Item\Memcached1;

/**
 * This is a very dependent on the Pool and the active item working. If it
 * breaks it most likely is something else.
 */
class PassiveTest extends \MistTest\Cache\_1_0\Item\BaseTest
{
    use \Mist\Cache\_1_0\Item\Validation;

	protected $memcached;

    protected function internalSet($key, $value)
    {
        $this->memcached->set($key, $value);
    }

    protected function internalGet($key)
    {
        return $this->memcached->get($key);
    }

    protected function internalHas($key)
    {
        $this->memcached->get($key);
        return $this->memcached->getResultCode() !== \Memcached::RES_NOTFOUND;
    }

    protected function internalDelete($key)
    {
        if (@$this->assertCacheKeyBase($key)) {
            $this->memcached->delete($key);
        }
    }

	protected function setUp()
	{
        if (class_exists(\Memcached::class) === false) {
            $this->markTestSkipped('Memcached not available');
        }

        $this->memcached = new \Memcached();
        $this->memcached->addServer('localhost', 11211);

        $this->store = new \Mist\Cache\_1_0\Pool\Memcached1($this->memcached);

        $this->storeMock = $this
			->getMockBuilder(\Mist\Cache\_1_0\Pool\Memcached1::class)
			->disableOriginalConstructor()
			->getMock();
    }

    protected function createItem($key, $value = null, $isHit = false)
    {
        return new \Mist\Cache\_1_0\Item\Memcached1\Passive(
        	$this->store, $key, $value, $isHit
        );
    }

    protected function createItemMockStore($key, $value = null, $isHit = false)
    {
    	return new \Mist\Cache\_1_0\Item\Memcached1\Passive(
    		$this->storeMock, $key, $value, $isHit
    	);
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
    	$mockItem = $this
    		->getMockBuilder(\Mist\Cache\_1_0\Item\Memcached1\Active::class)
    		->disableOriginalConstructor()
    		->getMock();
		$mockItem->expects($this->once())
			->method('set')
    		->with($this->anything(), $this->equalTo($ttlRaw));

        $this->storeMock
            ->expects($this->once())
            ->method('getItem')
            ->with($this->anything())
            ->will($this->returnValue($mockItem));;
    }

    public function testCreateHit()
    {
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key, $value, true);

        $this->assertSame($key, $item->getKey());
        $this->assertSame($value, $item->get());
        $this->assertTrue($item->isHit());
    }

    public function testCreateMiss()
    {
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key, null, false);

        $this->assertSame($key, $item->getKey());
        $this->assertNull($item->get());
        $this->assertFalse($item->isHit());
    }
}
