<?php

namespace Kanboard\Notification;

use Kanboard\Core\Base;

/**
 * Web Notification
 *
 * @package  notification
 * @author   Frederic Guillot
 */
class Web extends Base implements NotificationInterface
{
    /**
     * Send notification to a user
     *
     * @access public
     * @param  array     $user
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function notifyUser(array $user, $event_name, array $event_data)
    {
        $this->userUnreadNotification->create($user['id'], $event_name, $event_data);
    }

    /**
     * Send notification to a project
     *
     * @access public
     * @param  array     $project
     * @param  string    $event_name
     * @param  array     $event_data
     */
    public function notifyProject(array $project, $event_name, array $event_data)
    {
    }
}
