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
     * @param string $message
     * @param array  $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        file_put_contents('php://stderr', $this->formatMessage($level, $message, $context), FILE_APPEND);
    }
}
