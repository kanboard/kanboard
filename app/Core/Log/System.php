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
     * @param string|\Stringable $message
     * @param array  $context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        error_log('['.$level.'] '.$this->interpolate($message, $context));
    }
}
