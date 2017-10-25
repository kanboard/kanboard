<?php

namespace SimpleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Base class for loggers
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
abstract class Base extends AbstractLogger
{
    /**
     * Minimum log level for the logger
     *
     * @access private
     * @var    string
     */
    private $level = LogLevel::DEBUG;

    /**
     * Set minimum log level
     *
     * @access public
     * @param  string  $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Get minimum log level
     *
     * @access public
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Dump to log a variable (by example an array)
     *
     * @param mixed $variable
     */
    public function dump($variable)
    {
        $this->log(LogLevel::DEBUG, var_export($variable, true));
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @access protected
     * @param  string $message
     * @param  array $context
     * @return string
     */
    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();

        foreach ($context as $key => $val) {
          $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Format log message
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     * @return string
     */
    protected function formatMessage($level, $message, array $context = array())
    {
        return '['.date('Y-m-d H:i:s').'] ['.$level.'] '.$this->interpolate($message, $context).PHP_EOL;
    }
}
