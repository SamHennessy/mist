<?php

namespace Mist\Log\_1_0;

trait Building
{
    /**
     * @var \Zend_Log
     */
    protected $logger;

    /**
     * @var \Closure
     */
    protected $builder;

    /**
     * Must return an instance of \Monolog\Logger
     */
    public function setBuilder(\Closure $builder)
    {
        $this->builder = array($builder);
    }

    public function getLogger()
    {
        if ($this->logger) {
            return $this->logger;
        }

        $this->logger = $this->builder[0]();

        if ($this->validateBuiltLogger()) {
            return $this->logger;
        }

        throw new Exception(
            'Builder returned invalid object',
            Exception::LOG_1_0_BUILDING_1
        );
    }

    abstract protected function validateBuiltLogger();
}
