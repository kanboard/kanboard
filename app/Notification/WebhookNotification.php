<?php

namespace Kanboard\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Notification\NotificationInterface;

/**
 * Webhook Notification
 *
 * @package  Kanboard\Notification
 * @author   Frederic Guillot
 */
class WebhookNotification extends Base implements NotificationInterface
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
        $url = $this->configModel->get('webhook_url');
        $token = $this->configModel->get('webhook_token');

        if (! empty($url)) {
            if (strpos($url, '?') !== false) {
                $url .= '&token='.$token;
            } else {
                $url .= '?token='.$token;
            }

            $payload = array(
                'event_name' => $event_name,
                'event_data' => $event_data,
                'event_author' => ($this->userSession->isLogged() ? $this->userSession->getUsername() : NULL),
            );

            $this->httpClient->postJson($url, $payload);
        }
    }
}
