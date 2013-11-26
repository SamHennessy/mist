<?php


namespace MistTest\Cache\_1_0\Pool;

class Memcached1Test extends \PHPUnit_Framework_TestCase
{
    use \Mist\Cache\_1_0\Item\Validation;

    protected $memcached;

    protected $pool;

    protected $usedKeyList = array();

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

    protected function createPool()
    {
        return new \Mist\Cache\_1_0\Pool\Memcached1($this->memcached);
    }

    protected function rand()
    {
        return rand(10000, 1000000);
    }

    /**
     * 
     */
    public function testCreate()
    {
        $pool = $this->createPool();

        $this->assertInstanceOf(\Mist\Cache\_1_0\Pool::class, $pool);
        $this->assertInstanceOf(
            \Mist\Cache\_1_0\Pool\Memcached1::class, $pool
        );
    }

    public function testGetItemMiss()
    {
        $key = 'Key_'.$this->rand();

        $this->usedKeyList[] = $key;

        $pool = $this->createPool();

        $item = $pool->getItem($key);

        $this->assertInstanceOf(\Mist\Cache\_1_0\Item::class, $item);
        $this->assertSame($key, $item->getKey());
        $this->assertNull($item->get());
        $this->assertFalse($item->isHit());
    }

    public function testGetItemHit()
    {
        $key   = 'Key_'.$this->rand();
        $value = 'Value_'.$this->rand();

        $this->usedKeyList[] = $key;

        $this->internalSet($key, $value);

        $pool = $this->createPool();

        $item = $pool->getItem($key);

        $this->assertInstanceOf(\Mist\Cache\_1_0\Item::class, $item);
        $this->assertSame($key, $item->getKey());
        $this->assertSame($value, $item->get());
        $this->assertTrue($item->isHit());
    }

    public function testGetItemsEmpty()
    {
        $pool = $this->createPool();

        $keyList = array(
            'Key_'.$this->rand(),
            'Key_'.$this->rand(),
            'Key_'.$this->rand(),
            'Key_'.$this->rand()
        );

        $this->usedKeyList = array_merge($this->usedKeyList, $keyList);

        $itemList = $pool->getItems($keyList);

        $this->assertInstanceOf(\Traversable::class, $itemList);
    }

    public function testGetItemsHit()
    {
        $pool = $this->createPool();

        $dataList = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $this->usedKeyList =
            array_merge($this->usedKeyList, array_keys($dataList));

        foreach ($dataList as $key => $value) {
            $this->internalSet($key, $value);
        }

        $itemList = $pool->getItems(array_keys($dataList));

        $count      = 0;
        $checkQueue = $dataList;
        foreach ($itemList as $key => $item) {
            $count++;

            $this->assertInstanceOf(\Mist\Cache\_1_0\Item::class, $item);

            $checkKey   = key($checkQueue);
            $checkValue = array_shift($checkQueue);

            $this->assertSame($checkKey, $key);
            $this->assertSame($checkKey, $item->getKey());
            $this->assertSame($checkValue, $item->get());
        }

        $this->assertSame(count($dataList), $count);
    }

    public function testGetItemsOrder()
    {

        $pool = $this->createPool();

        $dataList = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $this->usedKeyList =
            array_merge($this->usedKeyList, array_keys($dataList));

        foreach ($dataList as $key => $value) {
            $this->internalSet($key, $value);
        }

        // Shuffle the keys
        $keyList = array_keys($dataList);
        shuffle($keyList);

        $itemList = $pool->getItems($keyList);
        foreach ($itemList as $key => $item) {

            $checkKey = array_shift($keyList);

            $this->assertSame($checkKey, $key);
            $this->assertSame($checkKey, $item->getKey());
        }

        // Again, but different
        $keyList = array_keys($dataList);
        $keyList = array_reverse($keyList);

        $itemList = $pool->getItems($keyList);
        foreach ($itemList as $key => $item) {

            $checkKey = array_shift($keyList);

            $this->assertSame($checkKey, $key);
            $this->assertSame($checkKey, $item->getKey());
        }
    }

    public function testGetItemsSecondCall1()
    {
        $pool = $this->createPool();

        $dataList1 = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $dataList2 = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $this->usedKeyList =
            array_merge(
                $this->usedKeyList,
                array_keys($dataList1),
                array_keys($dataList2)
            );

        foreach ($dataList1 as $key => $value) {
            $this->internalSet($key, $value);
        }
        foreach ($dataList2 as $key => $value) {
            $this->internalSet($key, $value);
        }

        $result1 = $pool->getItems(array_keys($dataList1));
        $result2 = $pool->getItems(array_keys($dataList2));

        $keyList1 = array_keys($dataList1);
        $keyList2 = array_keys($dataList2);

        $this->assertSame(array_shift($keyList1), $result1->key());
        $result1->next();
        $this->assertSame(array_shift($keyList1), $result1->key());

        $this->assertSame(array_shift($keyList2), $result2->key());
        $result2->next();
        $this->assertSame(array_shift($keyList2), $result2->key());
    }

    public function testGetItemsSecondCall2()
    {
        $pool = $this->createPool();

        $dataList1 = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $dataList2 = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $this->usedKeyList =
            array_merge(
                $this->usedKeyList,
                array_keys($dataList1),
                array_keys($dataList2)
            );

        foreach ($dataList1 as $key => $value) {
            $this->internalSet($key, $value);
        }
        foreach ($dataList2 as $key => $value) {
            $this->internalSet($key, $value);
        }

        $result1 = $pool->getItems(array_keys($dataList1));
        $result2 = $pool->getItems(array_keys($dataList2));

        $keyList1 = array_keys($dataList1);
        $keyList2 = array_keys($dataList2);

        $this->assertSame(array_shift($keyList2), $result2->key());
        $result2->next();
        $this->assertSame(array_shift($keyList2), $result2->key());

        $this->assertSame(array_shift($keyList1), $result1->key());
        $result1->next();
        $this->assertSame(array_shift($keyList1), $result1->key());
    }

    public function testGetItemsSecondCall3()
    {
        $pool = $this->createPool();

        $dataList1 = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $dataList2 = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $this->usedKeyList =
            array_merge(
                $this->usedKeyList,
                array_keys($dataList1),
                array_keys($dataList2)
            );

        foreach ($dataList1 as $key => $value) {
            $this->internalSet($key, $value);
        }
        foreach ($dataList2 as $key => $value) {
            $this->internalSet($key, $value);
        }

        $keyList1 = array_keys($dataList1);
        $keyList2 = array_keys($dataList2);

        $result1 = $pool->getItems(array_keys($dataList1));
        $this->assertSame(array_shift($keyList1), $result1->key());

        $result1->next();
        $this->assertSame(array_shift($keyList1), $result1->key());

        $result2 = $pool->getItems(array_keys($dataList2));
        $this->assertSame(array_shift($keyList2), $result2->key());

        $result2->next();
        $this->assertSame(array_shift($keyList2), $result2->key());
    }

    public function testGetItemsSecondCallFail()
    {
        $pool = $this->createPool();

        $dataList1 = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $dataList2 = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $this->usedKeyList =
            array_merge(
                $this->usedKeyList,
                array_keys($dataList1),
                array_keys($dataList2)
            );

        foreach ($dataList1 as $key => $value) {
            $this->internalSet($key, $value);
        }
        foreach ($dataList2 as $key => $value) {
            $this->internalSet($key, $value);
        }

        $keyList1 = array_keys($dataList1);
        $keyList2 = array_keys($dataList2);

        $result1 = $pool->getItems(array_keys($dataList1));
        $this->assertSame(array_shift($keyList1), $result1->key());

        $result2 = $pool->getItems(array_keys($dataList2));
        $this->assertSame(array_shift($keyList2), $result2->key());

        $result1->next();
        $this->assertTrue(array_shift($keyList1) != $result1->key());

        $result2->next();
        $this->assertNull($result2->key());
    }

    public function testClearResturn()
    {
        $pool = $this->createPool();

        $this->assertSame($pool, $pool->clear());
    }

    public function testClear()
    {
        $pool = $this->createPool();

        $dataList = array(
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand(),
            'Key_'.$this->rand() => 'Value_'.$this->rand()
        );

        $this->usedKeyList =
            array_merge($this->usedKeyList, array_keys($dataList));

        foreach ($dataList as $key => $value) {
            $this->internalSet($key, $value);
        }

        $pool->clear();

        $count = 0;
        foreach ($pool->getItems(array_keys($dataList)) as $key => $item) {
            $count++;
        }

        $this->assertSame(0, $count);
    }
}
