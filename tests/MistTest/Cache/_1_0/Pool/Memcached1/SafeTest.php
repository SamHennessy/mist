<?php


namespace MistTest\Cache\_1_0\Pool\Memcached1;

class SafeTest extends \PHPUnit_Framework_TestCase
{
    use Base;

    protected function createPool()
    {
        return new \Mist\Cache\_1_0\Pool\Memcached1\Safe($this->memcached);
    }
}
