<?php

namespace Kanboard\Core\Log;

/**
 * Stdout logger
 *
 * @package Kanboard\Core\Log
 * @author  Frédéric Guillot
 */
class Stdout extends Base
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string|\Stringable $message
     * @param array  $context
     */
    public function log($level, string|\Stringable $message, array $context = array()): void
    {
        file_put_contents('php://stdout', $this->formatMessage($level, $message, $context), FILE_APPEND);
    }
}
