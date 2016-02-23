<?php

namespace SimpleLogger;

use RuntimeException;
use Psr\Log\LogLevel;

/**
 * Syslog Logger
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class Syslog extends Base
{
    /**
     * Setup Syslog configuration
     *
     * @param  string $syslog_ident       Application name
     * @param  int    $syslog_facility    See http://php.net/manual/en/function.openlog.php
     */
    public function __construct($syslog_ident = 'PHP', $syslog_facility = LOG_USER)
    {
        if (! openlog($syslog_ident, LOG_ODELAY | LOG_PID, $syslog_facility)) {
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
     */
    public function log($level, $message, array $context = array())
    {
        $syslog_priority = $this->getSyslogPriority($level);
        $syslog_message = $this->interpolate($message, $context);

        syslog($syslog_priority, $syslog_message);
    }
}
