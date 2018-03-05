<?php

namespace Kanboard\Core\Log;

/**
 * Built-in PHP Logger
 *
 * @package Kanboard\Core\Log
 * @author  Frédéric Guillot
 */
class System extends Base
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        error_log('['.$level.'] '.$this->interpolate($message, $context));
    }
}
