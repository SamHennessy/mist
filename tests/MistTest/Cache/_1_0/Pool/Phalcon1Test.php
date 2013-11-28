<?php

namespace MistTest\Cache\_1_0\Pool;

class Phalcon1Test extends \PHPUnit_Framework_TestCase
{
    use Base;

    protected $store;

    protected $pool;

    protected function setUp()
    {
        if (class_exists(\Phalcon\Cache\Frontend\Data::class) === false) {
            $this->markTestSkipped('Phalcon 1 not available');
        }

        $this->vfsRoot = \org\bovigo\vfs\vfsStream::setup();

        $frontCache = new \Phalcon\Cache\Frontend\None(array(
            'lifetime' => 172800
        ));

        $this->store = new \Phalcon\Cache\Backend\File($frontCache, array(
            'cacheDir' => \org\bovigo\vfs\vfsStream::url('root').'/'
        ));
    }

    protected function createPool()
    {
        return new \Mist\Cache\_1_0\Pool\Phalcon1($this->store);
    }

    protected function internalSet($key, $value)
    {
        $this->store->save($key, serialize($value));
    }

    protected function internalDelete($key)
    {
        if (@$this->assertCacheKeyBase($key)) {
            $this->store->delete($key);
        }
    }

    /**
     * Phalcon doesn't support clearing the cache
     *
     * @group unsupported
     */
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

        $this->assertSame(4, $count);
    }
}
