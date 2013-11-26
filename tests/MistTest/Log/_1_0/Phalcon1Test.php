<?php

namespace MistTest\Log\_1_0;

class Phalcon1Test extends \MistTest\Log\PsrLoggerInterfaceTest
{
	protected $logger;

    protected $stream;


	protected function setUp()
	{
        if (class_exists(\Phalcon\Logger::class) === false) {
            $this->markTestSkipped('Phalcon 1 not available');
        }

        $existed = in_array("var", stream_get_wrappers());
        if ($existed) {
            stream_wrapper_unregister("var");
        }
        stream_wrapper_register("var", \MistTest\VariableStream::class);
        $GLOBALS[__CLASS__] = '';

        $this->logger = new \Mist\Log\_1_0\Phalcon1();
        $this->logger->setBuilder(
            function () {
                $logger =  new \Phalcon\Logger\Adapter\Stream("var://".__CLASS__);
                $formatter = new \Phalcon\Logger\Formatter\Line("%type% %message%");
                $logger->setFormatter($formatter);
                return $logger;
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

		$this->assertInstanceOf(\Mist\Log\_1_0\Phalcon1::class, $logger);
		$this->assertInstanceOf(\Phalcon\Logger\AdapterInterface::class, $logger->getLogger());

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
            \Phalcon\Logger::DEBUG     => \Mist\Log\_1_0\Level::DEBUG,
            \Phalcon\Logger::INFO      => \Mist\Log\_1_0\Level::INFO,
            \Phalcon\Logger::NOTICE    => \Mist\Log\_1_0\Level::NOTICE,
            \Phalcon\Logger::WARNING   => \Mist\Log\_1_0\Level::WARNING,
            \Phalcon\Logger::ERROR     => \Mist\Log\_1_0\Level::ERROR,
            \Phalcon\Logger::CRITICAL  => \Mist\Log\_1_0\Level::CRITICAL,
            \Phalcon\Logger::ALERT     => \Mist\Log\_1_0\Level::ALERT,
            \Phalcon\Logger::EMERGENCE => \Mist\Log\_1_0\Level::EMERGENCY
        );

        $fp      = fopen("var://".__CLASS__, 'r');
        $logList = array();
    	while ($line = fgets($fp)) {
            $partList = explode(' ', substr($line, 0, -1), 2);
    		$logList[] = strtolower($partList[0])." $partList[1]";
    	}

    	return $logList;
    }

}
