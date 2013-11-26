<?php

namespace MistTest\Log\_1_0;

class Zend2Test extends \MistTest\Log\PsrLoggerInterfaceTest
{
	protected $logger;

    protected $writter;


	protected function setUp()
	{
        $this->writer = new \Zend\Log\Writer\Mock();

        $this->logger = new \Mist\Log\_1_0\Zend2();
        $this->logger->setBuilder(
            function () {
                $log = new \Zend\Log\Logger();
                $log->addWriter($this->writer);
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

		$this->assertInstanceOf(\Mist\Log\_1_0\Zend2::class, $logger);
		$this->assertInstanceOf(\Zend\Log\Logger::class, $logger->getLogger());
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
            \Zend\Log\Logger::DEBUG  => \Mist\Log\_1_0\Level::DEBUG,
            \Zend\Log\Logger::INFO   => \Mist\Log\_1_0\Level::INFO,
            \Zend\Log\Logger::NOTICE => \Mist\Log\_1_0\Level::NOTICE,
            \Zend\Log\Logger::WARN   => \Mist\Log\_1_0\Level::WARNING,
            \Zend\Log\Logger::ERR    => \Mist\Log\_1_0\Level::ERROR,
            \Zend\Log\Logger::CRIT   => \Mist\Log\_1_0\Level::CRITICAL,
            \Zend\Log\Logger::ALERT  => \Mist\Log\_1_0\Level::ALERT,
            \Zend\Log\Logger::EMERG  => \Mist\Log\_1_0\Level::EMERGENCY
        );

    	$logList = array();
    	foreach ($this->writer->events as $record) {
    		$logList[]
    			= $levelMap[$record['priority']].' '.$record['message'];
    	}

    	return $logList;
    }

}
