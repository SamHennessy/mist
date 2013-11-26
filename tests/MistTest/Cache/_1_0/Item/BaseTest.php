<?php

namespace MistTest\Cache\_1_0\Item;

/**
 * 
 */
abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    protected $usedKeyList = array();

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        foreach ($this->usedKeyList as $key) {
            if (
                is_object($key) === false
                && is_array($key) === false
            ) {
                $this->internalDelete($key);
            }
        }
    }

    protected function rand()
    {
        return time() + rand(1, 1000);
    }

    /**
     *
     * @example extract($this->createRandKeyAndValue());
     *     echo $key, ' => ', $value;
     *
     */
    protected function createRandKeyAndValue()
    {
        $data = array(
            'key'   => 'Test_Key_'.$this->rand(),
            'value' => 'Test_Value_'.$this->rand()
        );

        $this->usedKeyList[] = $data['key'];

        return $data;
    }

    public function testGetKey()
    {
        $key = (string)$this->rand();
        $item = $this->createItem($key);
        $this->assertSame($key, $item->getKey());
    }

    /**
     *
     */
    public function testCreateKeyLengthMin()
    {
        $key  = '';
        $item = $this->createItem($key);
        $this->assertSame($key, $item->getKey());
    }

    /**
     *
     */
    public function testCreateKeyLengthMinSupport()
    {
        $key  = str_repeat('A', 64);
        $item = $this->createItem($key);
        $this->assertSame($key, $item->getKey());
    }

    public function provideKeyVariations()
    {
        return array(
            array(true, (string)123),
            array(true, 'abc'),
            array(true, 'ABC'),
            array(true, '123abcABC'),
            array(true, '123_abc_ABC'),
            array(true, '_'),
            array(true, (string)(new ToString)),

            array(false, 123),
            array(false, 1.1),
            array(false, new \stdClass),
            array(false, new ToString),
            array(false, '{'),
            array(false, '}'),
            array(false, '('),
            array(false, ')'),
            array(false, '/'),
            array(false, '\\'),
            array(false, ':'),
        );
    }

    /**
     * @dataProvider provideKeyVariations
     */
    public function testKeyVariations($isValid, $key)
    {
        if ($isValid === false) {
            $this->setExpectedException(\PHPUnit_Framework_Error::class);
        }

        $item = $this->createItem($key);
        $this->assertSame($key, $item->getKey());
    }

    public function testKeyInvalidException()
    {
        $code = false;
        try {

            @$this->createItem(new \stdClass);

        } catch (\Mist\Cache\_1_0\Item\InvalidArgumentException $exception) {

            $code = $exception->getCode();

        }

        $this->assertSame(
            \Mist\Cache\_1_0\Exception::CACHE_1_0_INVALID_KEY,
            $code
        );
    }

    public function testSet()
    {
        extract($this->createRandKeyAndValue());

        $item = $this->createItem($key);
        $item->set($value);

        $this->assertSame($value, $this->internalGet($key));
    }

    public function testInvalidData()
    {
        $fh = fopen('php://memory', 'r');

        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);

        $this->setExpectedException(\PHPUnit_Framework_Error::class);

        $item->set($fh);
    }

    public function testInvalidTtlError()
    {
        extract($this->createRandKeyAndValue());
        $item  = $this->createItem($key);
        $value = $this->rand();

        $this->setExpectedException(\PHPUnit_Framework_Error::class);

        $item->set($value, 'invalid ttl');
    }

    public function testIsHitSetHit()
    {
        extract($this->createRandKeyAndValue());
        $item  = $this->createItem($key);
        $item->set($value);

        $this->assertTrue($item->isHit());
    }

    public function testIsHitSetNull()
    {
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);
        $item->set(null);

        $this->assertTrue($item->isHit());
        $this->assertNull($item->get());
        $this->assertNull($this->internalGet($key));
    }

    public function testIsHitSetNull2()
    {
        extract($this->createRandKeyAndValue());

        $item = $this->createItem($key);

        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    /**
     * 
     */
    public function testDeleteReturnValue()
    {
        extract($this->createRandKeyAndValue());

        $item = $this->createItem($key);
        $this->assertSame($item, $item->delete());
    }

    public function testDelete()
    {
        extract($this->createRandKeyAndValue());

        $item = $this->createItem($key);
        $item->set($value);
        $item->delete();

        $this->assertFalse($this->internalHas($key));
    }

    public function testSetDeleteHasAndGet()
    {
        extract($this->createRandKeyAndValue());

        $item = $this->createItem($key);
        $item->set($value);
        $item->delete();
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    public function testExistsMiss()
    {
        extract($this->createRandKeyAndValue());
        $item = $this->createItem($key);
        $this->assertFalse($item->exists());
    }

    public function testExistsHit()
    {
        extract($this->createRandKeyAndValue());
        $item  = $this->createItem($key);

        $this->internalSet($key, $value);

        $this->assertTrue($item->exists());
    }

    public function testTtlNone()
    {
        extract($this->createRandKeyAndValue());
        $item  = $this->createItemMockStore($key);
        $this->setTtlExpectation($key, $value, 0, null);

        $item->set($value);
    }

    public function testTtlNull()
    {
        extract($this->createRandKeyAndValue());
        $item  = $this->createItemMockStore($key);
        $this->setTtlExpectation($key, $value, 0, null);

        $item->set($value, null);
    }

    public function testTtlZero()
    {
        extract($this->createRandKeyAndValue());
        $item  = $this->createItemMockStore($key);
        $this->setTtlExpectation($key, $value, 0, null);

        $item->set($value, 0);
    }

    public function testTtlSeconds()
    {
        extract($this->createRandKeyAndValue());
        $ttl  = rand(60, 600);
        $item = $this->createItemMockStore($key);

        $this->setTtlExpectation($key, $value, $ttl, $ttl);

        $item->set($value, $ttl);
    }

    public function testTtlDatetime()
    {
        extract($this->createRandKeyAndValue());
        $ttl      = rand(60, 600);
        $dateTime = new \Datetime('@'.strtotime('+'.$ttl.' seconds'));
        $item     = $this->createItemMockStore($key);

        $this->setTtlExpectation($key, $value, $ttl, $dateTime);

        $item->set($value, $dateTime);
    }
}
