<?php

namespace Kanboard\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Notification\NotificationInterface;

/**
 * Activity Stream Notification
 *
 * @package  Kanboard\Notification
 * @author   Frederic Guillot
 */
class ActivityStreamNotification extends Base implements NotificationInterface
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
        if ($this->userSession->isLogged()) {
            $this->projectActivityModel->createEvent(
                $project['id'],
                $event_data['task']['id'],
                $this->userSession->getId(),
                $event_name,
                $event_data
            );
        }
    }
}
