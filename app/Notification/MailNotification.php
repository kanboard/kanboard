<?php

namespace Kanboard\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Notification\NotificationInterface;

/**
 * Email Notification
 *
 * @package  Kanboard\Notification
 * @author   Frederic Guillot
 */
class MailNotification extends Base implements NotificationInterface
{
    /**
     * Notification type
     *
     * @var string
     */
    const TYPE = 'email';

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
        if (! empty($user['email'])) {
            $headers = [];

            if (isset($event_data['task']['id'])) {
                $headers['In-Reply-To'] = hash('sha256', 'KB'.$this->configModel->get('application_url').$event_data['task']['id']);
            }

            $this->emailClient->send(
                $user['email'],
                $user['name'] ?: $user['username'],
                $this->getMailSubject($event_name, $event_data),
                $this->getMailContent($event_name, $event_data),
                null,
                null,
                $headers
            );
        }
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

    /**
     * Get the mail content for a given template name
     *
     * @access public
     * @param  string    $event_name  Event name
     * @param  array     $event_data  Event data
     * @return string
     */
    public function getMailContent($event_name, array $event_data)
    {
        return $this->template->render('notification/'.str_replace('.', '_', $event_name), $event_data);
    }

    /**
     * Get the mail subject for a given template name
     *
     * @access public
     * @param  string $eventName Event name
     * @param  array  $eventData Event data
     * @return string
     */
    public function getMailSubject($eventName, array $eventData)
    {
        return sprintf(
            '[%s] %s',
            isset($eventData['project_name']) ? $eventData['project_name'] : $eventData['task']['project_name'],
            $this->notificationModel->getTitleWithoutAuthor($eventName, $eventData)
        );
    }
}
