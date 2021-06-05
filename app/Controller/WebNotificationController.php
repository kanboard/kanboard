<?php

namespace Kanboard\Controller;

/**
 * Web notification controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class WebNotificationController extends BaseController
{
    /**
     * My notifications
     *
     * @access public
     */
    public function show()
    {
        $user = $this->getUser();
        $notifications = $this->userUnreadNotificationModel->getAll($user['id']);

        $this->response->html($this->template->render('web_notification/show', array(
            'notifications'    => $notifications,
            'nb_notifications' => count($notifications),
            'user'             => $user,
        )));
    }

    /**
     * Mark all notifications as read
     *
     * @access public
     */
    public function flush()
    {
        $this->checkReusableGETCSRFParam();
        $userId = $this->getUserId();
        $this->userUnreadNotificationModel->markAllAsRead($userId);
        $this->show();
    }

    /**
     * Mark a notification as read
     *
     * @access public
     */
    public function remove()
    {
        $this->checkReusableGETCSRFParam();
        $user_id = $this->getUserId();
        $notification_id = $this->request->getIntegerParam('notification_id');
        $this->userUnreadNotificationModel->markAsRead($user_id, $notification_id);
        $this->show();
    }

    /**
     * Redirect to the task and mark notification as read
     */
    public function redirect()
    {
        $user_id = $this->getUserId();
        $notification_id = $this->request->getIntegerParam('notification_id');

        $notification = $this->userUnreadNotificationModel->getById($notification_id);
        $this->userUnreadNotificationModel->markAsRead($user_id, $notification_id);

        if (empty($notification)) {
            $this->show();
        } elseif ($this->helper->text->contains($notification['event_name'], 'comment')) {
            $this->response->redirect($this->helper->url->to(
                'TaskViewController',
                'show',
                array('task_id' => $this->notificationModel->getTaskIdFromEvent($notification['event_name'], $notification['event_data'])),
                'comment-'.$notification['event_data']['comment']['id']
            ));
        } else {
            $this->response->redirect($this->helper->url->to(
                'TaskViewController',
                'show',
                array('task_id' => $this->notificationModel->getTaskIdFromEvent($notification['event_name'], $notification['event_data']))
            ));
        }
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
