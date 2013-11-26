<?php

namespace Mist\Log\_1_0;

class Zend2 implements Logger
{
    use Logging, Interpolate, Building;

    protected function validateBuiltLogger()
    {
        return $this->logger instanceof \Zend\Log\Logger;
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
            Level::DEBUG     => \Zend\Log\Logger::DEBUG,
            Level::INFO      => \Zend\Log\Logger::INFO,
            Level::NOTICE    => \Zend\Log\Logger::NOTICE,
            Level::WARNING   => \Zend\Log\Logger::WARN,
            Level::ERROR     => \Zend\Log\Logger::ERR,
            Level::CRITICAL  => \Zend\Log\Logger::CRIT,
            Level::ALERT     => \Zend\Log\Logger::ALERT,
            Level::EMERGENCY => \Zend\Log\Logger::EMERG
        );

        if (isset($levelMap[$level]) === false) {
            throw new \Psr\Log\InvalidArgumentException(
                'Invalid log level used',
                Exception::LOG_1_0_INVALID_LOG_LEVEL
            );
        }

        $message = $this->interpolate((string)$message, $context);
        $this->getLogger()->log($levelMap[$level], $message, $context);
    }
}
