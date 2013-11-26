<?php

namespace Mist\Log\_1_0;

class Zend1 implements Logger
{
    use Logging, Interpolate, Building;

    protected function validateBuiltLogger()
    {
        return $this->logger instanceof \Zend_Log;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        static $levelMap = array(
            Level::DEBUG     => \Zend_Log::DEBUG,
            Level::INFO      => \Zend_Log::INFO,
            Level::NOTICE    => \Zend_Log::NOTICE,
            Level::WARNING   => \Zend_Log::WARN,
            Level::ERROR     => \Zend_Log::ERR,
            Level::CRITICAL  => \Zend_Log::CRIT,
            Level::ALERT     => \Zend_Log::ALERT,
            Level::EMERGENCY => \Zend_Log::EMERG
        );

        if (isset($levelMap[$level]) === false) {
            throw new \Psr\Log\InvalidArgumentException(
                'Invalid log level used',
                Exception::LOG_1_0_INVALID_LOG_LEVEL
            );
        }

        $message = $this->interpolate((string)$message, $context);
        $this->getLogger()->log($message, $levelMap[$level], $context);
    }
}
