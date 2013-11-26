<?php

namespace MistTest\Cache\_1_0\Pool;

class Zend2Test extends \PHPUnit_Framework_TestCase
{
    protected $memoryCache;

    protected function getPool($setBuilderPool = true)
    {
        $pool = new \Mist\Cache\_1_0\Pool\Zend2();

        $this->memoryCache = \Zend\Cache\StorageFactory::factory(
            array(
                'adapter' => array(
                    'name' => 'memory'
                ),
            )
        );

        if ($setBuilderPool) {
            $pool->setBuilderPool(function() {
                return $this->memoryCache;
            });
        }

        return $pool;
    }

    /**
     * 
     */
    public function testCreate()
    {
        $pool = $this->getPool();

        $this->assertInstanceOf(\Mist\Cache\_1_0\Pool::class, $pool);
        $this->assertInstanceOf(\Mist\Cache\_1_0\Pool\Zend2::class, $pool);
    }

    public function testNoBuilderError()
    {
        $this->setExpectedException(
            \Mist\Cache\_1_0\Pool\Exception::class,
            '',
            \Mist\Exception::CACHE_1_0_POOL_BUILDING_2
        );

        $pool = $this->getPool(false);

        $pool->getItem(time());
    }

    public function testInvalidBuilderError()
    {
        $this->setExpectedException(
            \Mist\Cache\_1_0\Pool\Exception::class,
            '',
            \Mist\Exception::CACHE_1_0_POOL_BUILDING_1
        );

        $pool = $this->getPool(false);

        $pool->setBuilderPool(function(){});

        $pool->getItem(time());
    }

    public function testGetPool()
    {
        $this->assertInstanceOf(
            \Zend\Cache\Storage\Adapter\Memory::class,
            $this->getPool()->getPool()
        );
    }



}
