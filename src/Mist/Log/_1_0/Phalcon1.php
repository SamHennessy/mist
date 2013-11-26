<?php

namespace Mist\Log\_1_0;

class Phalcon1 implements Logger
{
    use Logging, Interpolate, Building;

    protected function validateBuiltLogger()
    {
        return $this->logger instanceof \Phalcon\Logger\AdapterInterface;
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
            Level::DEBUG     => \Phalcon\Logger::DEBUG,
            Level::INFO      => \Phalcon\Logger::INFO,
            Level::NOTICE    => \Phalcon\Logger::NOTICE,
            Level::WARNING   => \Phalcon\Logger::WARNING,
            Level::ERROR     => \Phalcon\Logger::ERROR,
            Level::CRITICAL  => \Phalcon\Logger::CRITICAL,
            Level::ALERT     => \Phalcon\Logger::ALERT,
            Level::EMERGENCY => \Phalcon\Logger::EMERGENCE
        );

        if (isset($levelMap[$level]) === false) {
            throw new \Psr\Log\InvalidArgumentException(
                'Invalid log level used',
                Exception::LOG_1_0_INVALID_LOG_LEVEL
            );
        }

        $message = $this->interpolate((string)$message, $context);
        $this->getLogger()->log($message, $levelMap[$level]);
    }
}
