<?php

namespace Kanboard\Core\Log;

use RuntimeException;
use Psr\Log\LogLevel;

/**
 * Syslog Logger
 *
 * @package Kanboard\Core\Log
 * @author  Frédéric Guillot
 */
class Syslog extends Base
{
    /**
     * Setup Syslog configuration
     *
     * @param  string $ident    Application name
     * @param  int    $facility See http://php.net/manual/en/function.openlog.php
     */
    public function __construct($ident = 'PHP', $facility = LOG_USER)
    {
        if (! openlog($ident, LOG_ODELAY | LOG_PID, $facility)) {
            throw new RuntimeException('Unable to connect to syslog.');
        }
    }

    /**
     * Get syslog priority according to Psr\LogLevel
     *
     * @param  mixed  $level
     * @return integer
     */
    public function getSyslogPriority($level)
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
                return LOG_EMERG;
            case LogLevel::ALERT:
                return LOG_ALERT;
            case LogLevel::CRITICAL:
                return LOG_CRIT;
            case LogLevel::ERROR:
                return LOG_ERR;
            case LogLevel::WARNING:
                return LOG_WARNING;
            case LogLevel::NOTICE:
                return LOG_NOTICE;
            case LogLevel::INFO:
                return LOG_INFO;
        }

        return LOG_DEBUG;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed   $level
     * @param  string  $message
     * @param  array   $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $syslogPriority = $this->getSyslogPriority($level);
        $syslogMessage = $this->interpolate($message, $context);

        syslog($syslogPriority, $syslogMessage);
    }
}
