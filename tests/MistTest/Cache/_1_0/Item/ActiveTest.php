<?php

namespace MistTest\Cache\_1_0\Item;

/**
 * The following test is written is such away that assume that memory caching
 * will be layered on top of internal cache storage. As such this is not a
 * true unit test. It requires access to the internal system also.
 * 
 */
abstract class ActiveTest extends BaseTest
{

    protected abstract function internalSet($key, $value);

    protected abstract function internalGet($key);

    protected abstract function internalHas($key);

    protected abstract function internalDelete($key);

    protected abstract function createItem($key);

    protected abstract function createItemMockStore($key);

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
    protected abstract function setTtlExpectation($key, $value, $ttlSeconds, $ttlRaw);

    /**
     *
     */
    public function testCreate()
    {
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);

        $this->assertInstanceOf(\Mist\Cache\_1_0\Item::class, $item);
    }

    public function testSetReturnOnSuccess()
    {
        // Phalcon\Cache\Exception
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);
        $this->assertTrue($item->set($this->rand()));
    }

    public function testSetReturnOnFail()
    {
        // Phalcon\Cache\Exception
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);
        $this->assertFalse(@$item->set(fopen('php://memory', 'r')));
    }

    /**
     * @dataProvider provideKeyVariations
     */
    public function testKeyVariationsInternal($isValid, $key)
    {
        $this->usedKeyList[] = $key;

        if ($isValid === false) {
            $this->setExpectedException(\PHPUnit_Framework_Error::class);
        }

        $item = $this->createItem($key);
        $item->set($this->rand());

        $this->assertTrue($this->internalHas($key));
    }

    public function testGet()
    {
        extract($this->createRandKeyAndValue());

        $this->internalSet($key, $value);

        $item = $this->createItem($key);

        $this->assertSame($value, $item->get());
    }

    public function testGetValueDefault()
    {
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);

        $this->assertNull($item->get());
    }

    /**
     * @dataProvider provideValidValue
     */
    public function testSetAndGetValidTypes($key, $value)
    {
        $this->usedKeyList[$key] = $key;

        $item  = $this->createItem($key);
        $item->set($value);
        if (is_object($value)) {
            $this->assertEquals($value, $item->get());
        } else {
            $this->assertSame($value, $item->get());
        }
    }

    protected function getValidValueData()
    {
        $validValueData = array(
            'rand'      => $this->rand(),
            'string'    => 'string',
            'zero'      => 0,
            'max_int'   => PHP_INT_MAX,
            'float'     => 1.1,
            'float_big' => PHP_INT_MAX + 1,
            'true'      => true,
            'false'     => false,
            'null'      => null
        );

        // I don't want object in these
        $validValueData['array_index'] = array_values($validValueData);
        $validValueData['array_assoc'] = $validValueData;
        $validValueData['array_multi'] = array(
            $validValueData, array($validValueData)
        );

        // Objects
        $validValueData['stdClass'] = new \stdClass;
        $obj = new \stdClass;
        $obj->foo = new \Exception('message', 123);
        $validValueData['stdClass2'] = $obj;

        return $validValueData;
    }

    public function provideValidValue()
    {
        $validList = array();
        foreach ($this->getValidValueData() as $key => $value) {
            $validList[$key] = array($key, $value);
        }
        return $validList;
    }

    public function testIsHitMiss()
    {
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);

        $this->assertFalse($item->isHit());
    }

    /**
     * 
     */
    public function testIsHit()
    {
        extract($this->createRandKeyAndValue());

        $this->internalSet($key, serialize($value));

        $item = $this->createItem($key);

        $this->assertTrue($item->isHit());
    }

    /**
     * Shows you could be had gotten a value that isHot says is in the cache
     * but really it has since been deleted
     */
    public function testGetIsHitExistsRaceCondition()
    {
        extract($this->createRandKeyAndValue());
        $item  = $this->createItem($key);

        $this->internalSet($key, $value);
        $item->get();

        $this->internalDelete($key);

        $this->assertTrue($item->isHit());
        $this->assertSame($value, $value);

        $this->assertFalse($item->exists());
    }

    /**
     * Show that exists will say the cache items is there but when get is
     * called the value is not longer in the cache
     */
    public function testGetIsHitExistsRaceCondition2()
    {
        extract($this->createRandKeyAndValue());
        $item  = $this->createItem($key);

        $this->internalSet($key, $value);

        $this->assertTrue($item->exists());

        $this->internalDelete($key);

        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    /**
     * Call isHit, then delete internally. Should still be able to get the value
     */
    public function testGetIsHitExistsRaceCondition3()
    {
        extract($this->createRandKeyAndValue());

        $this->internalSet($key, $value);

        $item  = $this->createItem($key);

        $this->assertTrue($item->isHit());

        $this->internalDelete($key);

        $this->assertSame($value, $item->get());
        $this->assertTrue($item->isHit());
    }

    /**
     * This will catch clients that can't check for success on gets
     */
    public function testIsHitSetNull3()
    {
        extract($this->createRandKeyAndValue());
        $this->internalSet($key, null);
        $item = $this->createItem($key);

        $this->assertTrue($item->isHit());
        $this->assertNull($item->get());
        $this->assertNull($this->internalGet($key));
    }
}
