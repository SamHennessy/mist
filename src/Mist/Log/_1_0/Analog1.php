<?php

namespace Mist\Log\_1_0;

class Analog1 implements Logger
{
    use Logging, Interpolate, Building;

    protected function validateBuiltLogger()
    {
        return $this->logger instanceof \Analog\Logger;
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
            Level::DEBUG     => \Psr\Log\LogLevel::DEBUG,
            Level::INFO      => \Psr\Log\LogLevel::INFO,
            Level::NOTICE    => \Psr\Log\LogLevel::NOTICE,
            Level::WARNING   => \Psr\Log\LogLevel::WARNING,
            Level::ERROR     => \Psr\Log\LogLevel::ERROR,
            Level::CRITICAL  => \Psr\Log\LogLevel::CRITICAL,
            Level::ALERT     => \Psr\Log\LogLevel::ALERT,
            Level::EMERGENCY => \Psr\Log\LogLevel::EMERGENCY
        );

        if (isset($levelMap[$level]) === false) {
            throw new \Psr\Log\InvalidArgumentException(
                'Invalid log level used',
                Exception::LOG_1_0_INVALID_LOG_LEVEL
            );
        }

        $this->getLogger()->log(
            $levelMap[$level],
            $this->interpolate((string)$message, $context)
        );
    }
}
