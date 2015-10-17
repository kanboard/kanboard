<?php

namespace Kanboard\Controller;

/**
 * Web notification controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class WebNotification extends Base
{
    /**
     * Mark all notifications as read
     *
     * @access public
     */
    public function flush()
    {
        $user_id = $this->getUserId();

        $this->userUnreadNotification->markAllAsRead($user_id);
        $this->response->redirect($this->helper->url->to('app', 'notifications', array('user_id' => $user_id)));
    }

    /**
     * Mark a notification as read
     *
     * @access public
     */
    public function remove()
    {
        $user_id = $this->getUserId();
        $notification_id = $this->request->getIntegerParam('notification_id');

        $this->userUnreadNotification->markAsRead($user_id, $notification_id);
        $this->response->redirect($this->helper->url->to('app', 'notifications', array('user_id' => $user_id)));
    }

    private function getUserId()
    {
        $user_id = $this->request->getIntegerParam('user_id');

        if (! $this->userSession->isAdmin() && $user_id != $this->userSession->getId()) {
            $user_id = $this->userSession->getId();
        }

        return $user_id;
    }
}
