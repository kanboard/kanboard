<?php

namespace Kanboard\Controller;

/**
 * User Status Controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class UserStatus extends Base
{
    /**
     * Confirm remove a user
     *
     * @access public
     */
    public function confirmRemove()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->user('user_status/remove', array(
            'user' => $user,
        )));
    }

    /**
     * Remove a user
     *
     * @access public
     */
    public function remove()
    {
        $user = $this->getUser();
        $this->checkCSRFParam();

        if ($this->user->remove($user['id'])) {
            $this->flash->success(t('User removed successfully.'));
        } else {
            $this->flash->failure(t('Unable to remove this user.'));
        }

        $this->response->redirect($this->helper->url->to('user', 'index'));
    }

    /**
     * Confirm enable a user
     *
     * @access public
     */
    public function confirmEnable()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->user('user_status/enable', array(
            'user' => $user,
        )));
    }

    /**
     * Enable a user
     *
     * @access public
     */
    public function enable()
    {
        $user = $this->getUser();
        $this->checkCSRFParam();

        if ($this->user->enable($user['id'])) {
            $this->flash->success(t('User activated successfully.'));
        } else {
            $this->flash->failure(t('Unable to enable this user.'));
        }

        $this->response->redirect($this->helper->url->to('user', 'index'));
    }

    /**
     * Confirm disable a user
     *
     * @access public
     */
    public function confirmDisable()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->user('user_status/disable', array(
            'user' => $user,
        )));
    }

    /**
     * Disable a user
     *
     * @access public
     */
    public function disable()
    {
        $user = $this->getUser();
        $this->checkCSRFParam();

        if ($this->user->disable($user['id'])) {
            $this->flash->success(t('User disabled successfully.'));
        } else {
            $this->flash->failure(t('Unable to disable this user.'));
        }

        $this->response->redirect($this->helper->url->to('user', 'index'));
    }
}
