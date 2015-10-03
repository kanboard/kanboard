<?php

namespace Core;

/**
 * Notification Interface
 *
 * @package  core
 * @author   Frederic Guillot
 */
interface NotificationInterface
{
    /**
     * Send notification to someone
     *
     * @access public
     * @param  array     $user
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function send(array $user, $event_name, array $event_data);
}
