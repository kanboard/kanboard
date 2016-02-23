<?php

namespace SimpleLogger;

use RuntimeException;

/**
 * File logger
 *
 * @package SimpleLogger
 * @author  Frédéric Guillot
 */
class File extends Base
{
    private $filename = '';

    /**
     * Setup logger configuration
     *
     * @param  string $filename Output file
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        $line = '['.date('Y-m-d H:i:s').'] ['.$level.'] '.$this->interpolate($message, $context).PHP_EOL;

        if (file_put_contents($this->filename, $line, FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException('Unable to write to the log file.');
        }
    }
}
