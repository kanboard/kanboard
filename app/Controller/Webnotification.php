<?php

namespace Controller;

/**
 * Web notification controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Webnotification extends Base
{
    /**
     * Mark all notifications as read
     *
     * @access public
     */
    public function flush()
    {
        $user_id = $this->userSession->getId();

        $this->webNotification->markAllAsRead($user_id);
        $this->response->redirect($this->helper->url->to('app', 'notifications', array('user_id' => $user_id)));
    }

    /**
     * Mark a notification as read
     *
     * @access public
     */
    public function remove()
    {
        $user_id = $this->userSession->getId();
        $notification_id = $this->request->getIntegerParam('notification_id');

        $this->webNotification->markAsRead($user_id, $notification_id);
        $this->response->redirect($this->helper->url->to('app', 'notifications', array('user_id' => $user_id)));
    }
}
