<?php

namespace Mist\Log\_1_0;

class Monolog1 implements Logger
{
    use Logging, Interpolate, Building;

    protected function validateBuiltLogger()
    {
        return $this->logger instanceof \Monolog\Logger;
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
            Level::DEBUG     => \Monolog\Logger::DEBUG,
            Level::INFO      => \Monolog\Logger::INFO,
            Level::NOTICE    => \Monolog\Logger::NOTICE,
            Level::WARNING   => \Monolog\Logger::WARNING,
            Level::ERROR     => \Monolog\Logger::ERROR,
            Level::CRITICAL  => \Monolog\Logger::CRITICAL,
            Level::ALERT     => \Monolog\Logger::ALERT,
            Level::EMERGENCY => \Monolog\Logger::EMERGENCY
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
