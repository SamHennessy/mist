<?php

namespace MistTest\Log\_1_0;

class Zend1Test extends \MistTest\Log\PsrLoggerInterfaceTest
{
	protected $logger;

    protected $writter;


	protected function setUp()
	{
        @include_once('Zend/Log.php');
        @include_once('Zend/Log/Writer/Mock.php');
        if (class_exists(\Zend_Log::class) === false)
        {
            $this->markTestSkipped('Zend Framework 1 not available');
        }
        $this->writer = new \Zend_Log_Writer_Mock();

        $this->logger = new \Mist\Log\_1_0\Zend1();
        $this->logger->setBuilder(
            function () {
                $log = new \Zend_Log($this->writer);
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

		$this->assertInstanceOf(\Mist\Log\_1_0\Zend1::class, $logger);
		$this->assertInstanceOf(\Zend_log::class, $logger->getLogger());
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
        static $levelMap = array(
            \Zend_Log::DEBUG  => \Mist\Log\_1_0\Level::DEBUG,
            \Zend_Log::INFO   => \Mist\Log\_1_0\Level::INFO,
            \Zend_Log::NOTICE => \Mist\Log\_1_0\Level::NOTICE,
            \Zend_Log::WARN   => \Mist\Log\_1_0\Level::WARNING,
            \Zend_Log::ERR    => \Mist\Log\_1_0\Level::ERROR,
            \Zend_Log::CRIT   => \Mist\Log\_1_0\Level::CRITICAL,
            \Zend_Log::ALERT  => \Mist\Log\_1_0\Level::ALERT,
            \Zend_Log::EMERG  => \Mist\Log\_1_0\Level::EMERGENCY
        );

    	$logList = array();
    	foreach ($this->writer->events as $record) {
    		$logList[]
    			= $levelMap[$record['priority']].' '.$record['message'];
    	}

    	return $logList;
    }

}
