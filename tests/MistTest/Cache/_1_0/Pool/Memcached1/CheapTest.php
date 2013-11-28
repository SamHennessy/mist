<?php


namespace MistTest\Cache\_1_0\Pool\Memcached1;

class CheapTest extends \PHPUnit_Framework_TestCase
{
    use Base;

    protected function createPool()
    {
        return new \Mist\Cache\_1_0\Pool\Memcached1\Cheap($this->memcached);
    }

    /**
     * Cheap can't do this, so this is a failing test.
     *
     * If you need to do this use Safe
     *
     * @group unsupported
     */
    public function testGetItemsSecondCallGetData()
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
}
