<?php

namespace MistTest\Log\_1_0;

require_once('Zend/Log.php');
require_once('Zend/Log/Writer/Mock.php');

class Analog1Test extends \MistTest\Log\PsrLoggerInterfaceTest
{
	protected $logger;

    protected $buffer;

	protected function setUp()
	{

		$this->buffer = array();

		// Analog does this statically
		if ($this->logger === null) {

	        $this->logger = new \Mist\Log\_1_0\Analog1();
	        $this->logger->setBuilder(
	            function () {
	                $log = new \Analog\Logger();

	            	$handler = function ($info) {
	            		$level = $this->logger->getLogger()->convert_log_level(
            				$info['level'], true
            			);
	            		$this->buffer[] = "$level $info[message]";
	            	};
	                $log->handler($handler);

	                return $log;
	            }
	        );
		}
	}


    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
    	return $this->logger;
    }

	/**
	 * 
	 */
	public function testCreate()
	{
		$logger = $this->getLogger();

		$this->assertInstanceOf(\Mist\Log\_1_0\Analog1::class, $logger);
		$this->assertInstanceOf(\Analog\Logger::class, $logger->getLogger());
        $this->assertInstanceOf(\Mist\Log\_1_0\Logger::class, $logger);
        $this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $logger);
	}

    /**
     * This must return the log messages in order with a simple formatting: "<LOG LEVEL> <MESSAGE>"
     *
     * Example ->error('Foo') would yield "error Foo"
     *
     * @return string[]
     */
    public function getLogs()
    {
    	return $this->buffer;
    }

}
