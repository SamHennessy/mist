<?php

namespace Mist\Cache\_1_0\Pool;

trait Building
{
    /**
     * @var \Mist\Cache\_1_0\Pool
     */
    protected $pool;

    /**
     * @var \Closure
     */
    protected $builderMap;

    /**
     *
     */
    public function setBuilderPool(\Closure $builder)
    {
        $this->builderMap['pool'] = $builder;
    }

    /**
     *
     */
    public function setBuilderItem(\Closure $builder)
    {
        $this->builderMap['item'] = $builder;
    }

    public function getPool()
    {
        if ($this->pool) {
            return $this->pool;
        }

        if (isset($this->builderMap['pool']) === false) {
            throw new Exception(
                'No pool builder set',
                Exception::CACHE_1_0_POOL_BUILDING_2
            );
        }

        $this->pool = $this->builderMap['pool']();

        if ($this->validateBuilingPool($this->pool)) {
            return $this->pool;
        }

        throw new Exception(
            'Builder returned invalid object',
            Exception::CACHE_1_0_POOL_BUILDING_1
        );
    }

    abstract protected function validateBuilingPool($pool);

    abstract protected function validateBuilingItem($item);
}
