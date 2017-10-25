<?php

namespace SimpleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Handler for multiple loggers
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class Logger extends AbstractLogger implements LoggerAwareInterface
{
    /**
     * Logger instances
     *
     * @access private
     */
    private $loggers = array();

    /**
     * Get level priority
     *
     * @param  mixed  $level
     * @return integer
     */
    public function getLevelPriority($level)
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
                return 600;
            case LogLevel::ALERT:
                return 550;
            case LogLevel::CRITICAL:
                return 500;
            case LogLevel::ERROR:
                return 400;
            case LogLevel::WARNING:
                return 300;
            case LogLevel::NOTICE:
                return 250;
            case LogLevel::INFO:
                return 200;
        }

        return 100;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param  LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    /**
     * Proxy method to the real loggers
     *
     * @param  mixed   $level
     * @param  string  $message
     * @param  array   $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        foreach ($this->loggers as $logger) {
            if ($this->getLevelPriority($level) >= $this->getLevelPriority($logger->getLevel())) {
                $logger->log($level, $message, $context);
            }
        }
    }

    /**
     * Dump variables for debugging
     *
     * @param mixed $variable
     */
    public function dump($variable)
    {
        foreach ($this->loggers as $logger) {
            if ($this->getLevelPriority(LogLevel::DEBUG) >= $this->getLevelPriority($logger->getLevel())) {
                $logger->dump($variable);
            }
        }
    }
}
