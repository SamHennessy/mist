<?php

namespace MistTest\Log\_1_0;

class Monolog1Test extends \MistTest\Log\PsrLoggerInterfaceTest
{
	protected $logger;


	protected function setUp()
	{
    	$this->logger = new \Mist\Log\_1_0\Monolog1();
    	$this->logger->setBuilder(
			function () {
				$log = new \Monolog\Logger('test');
				$handler = new \Monolog\Handler\TestHandler();
				$log->pushHandler($handler);
				return $log;
			}
		);
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

		$this->assertInstanceOf(\Mist\Log\_1_0\Monolog1::class, $logger);
		$this->assertInstanceOf(\Mist\Log\_1_0\Logger::class, $logger);
		$this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $logger);
		$this->assertInstanceOf(\Monolog\Logger::class, $logger->getLogger());
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
    	$handler = $this->logger->getLogger()->popHandler();

    	$logList = array();
    	foreach ($handler->getRecords() as $record) {
    		$logList[]
    			= strtolower($record['level_name']).' '.$record['message'];
    	}

    	$this->logger->getLogger()->pushHandler($handler);

    	return $logList;
    }

}
