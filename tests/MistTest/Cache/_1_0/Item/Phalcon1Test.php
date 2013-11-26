<?php

namespace MistTest\Cache\_1_0\Item;

/**
 * The following test is written is such away that assume that memory caching
 * will be layered on top of internal cache storage. As such this is not a
 * true unit test. It requires access to the internal system also.
 * 
 */
class Phalcon1Test extends ActiveTest
{
	use \Mist\Cache\_1_0\Item\Validation;

	protected $store;

	protected $storeMock;

	protected $vfsRoot;

	protected function setUp()
	{
        if (class_exists(\Phalcon\Cache\Frontend\Data::class) === false) {
            $this->markTestSkipped('Phalcon 1 not available');
        }

		$this->vfsRoot = \org\bovigo\vfs\vfsStream::setup();

		$frontCache = new \Phalcon\Cache\Frontend\Data(array(
		    'lifetime' => 172800
		));

		$this->store = new \Phalcon\Cache\Backend\File($frontCache, array(
		    'cacheDir' => \org\bovigo\vfs\vfsStream::url('root').'/'
		));

		$this->storeMock = $this
			->getMockBuilder(\Phalcon\Cache\Backend\File::class)
			->disableOriginalConstructor()
			->getMock();
	}

	protected function internalSet($key, $value)
	{
		$this->store->save($key, $value);
	}

	protected function internalGet($key)
	{
		return $this->store->get($key);
	}

	protected function internalHas($key)
	{
		return $this->store->exists($key);
	}

	protected function internalDelete($key)
	{
		if (@$this->assertCacheKeyBase($key)) {
			$this->store->delete($key);
		}
	}

	protected function createItem($key)
	{
		return new \Mist\Cache\_1_0\Item\Phalcon1($this->store, $key);
	}

    protected function createItemMockStore($key)
    {
    	return new \Mist\Cache\_1_0\Item\Phalcon1($this->storeMock, $key);
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
            ->method('save')
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

	/**
	 * 
	 */
	public function testCreate()
	{
		extract($this->createRandKeyAndValue());
		$item = $this->createItem($key);

		$this->assertInstanceOf(\Mist\Cache\_1_0\Item::class, $item);
		$this->assertInstanceOf(\Mist\Cache\_1_0\Item\Phalcon1::class, $item);
	}
}
