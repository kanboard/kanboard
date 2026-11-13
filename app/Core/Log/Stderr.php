<?php

namespace Kanboard\Core\Log;

/**
 * Stderr logger
 *
 * @package Kanboard\Core\Log
 * @author  Frédéric Guillot
 */
class Stderr extends Base
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
        file_put_contents('php://stderr', $this->formatMessage($level, $message, $context), FILE_APPEND);
    }
}
