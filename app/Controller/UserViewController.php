<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;
use Kanboard\Model\Project as ProjectModel;

/**
 * Class UserViewController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class UserViewController extends BaseController
{
    /**
     * Public user profile
     *
     * @access public
     * @throws PageNotFoundException
     */
    public function profile()
    {
        $user = $this->user->getById($this->request->getIntegerParam('user_id'));

        if (empty($user)) {
            throw new PageNotFoundException();
        }

        $this->response->html($this->helper->layout->app('user_view/profile', array(
            'title' => $user['name'] ?: $user['username'],
            'user'  => $user,
        )));
    }

    /**
     * Display user information
     *
     * @access public
     */
    public function show()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/show', array(
            'user'      => $user,
            'timezones' => $this->timezone->getTimezones(true),
            'languages' => $this->language->getLanguages(true),
        )));
    }

    /**
     * Display timesheet
     *
     * @access public
     */
    public function timesheet()
    {
        $user = $this->getUser();

        $subtask_paginator = $this->paginator
            ->setUrl('UserViewController', 'timesheet', array('user_id' => $user['id'], 'pagination' => 'subtasks'))
            ->setMax(20)
            ->setOrder('start')
            ->setDirection('DESC')
            ->setQuery($this->subtaskTimeTracking->getUserQuery($user['id']))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $this->response->html($this->helper->layout->user('user_view/timesheet', array(
            'subtask_paginator' => $subtask_paginator,
            'user'              => $user,
        )));
    }

    /**
     * Display last password reset
     *
     * @access public
     */
    public function passwordReset()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/password_reset', array(
            'tokens' => $this->passwordReset->getAll($user['id']),
            'user'   => $user,
        )));
    }

    /**
     * Display last connections
     *
     * @access public
     */
    public function lastLogin()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/last', array(
            'last_logins' => $this->lastLogin->getAll($user['id']),
            'user'        => $user,
        )));
    }

    /**
     * Display user sessions
     *
     * @access public
     */
    public function sessions()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/sessions', array(
            'sessions' => $this->rememberMeSession->getAll($user['id']),
            'user'     => $user,
        )));
    }

    /**
     * Remove a "RememberMe" token
     *
     * @access public
     */
    public function removeSession()
    {
        $this->checkCSRFParam();
        $user = $this->getUser();
        $this->rememberMeSession->remove($this->request->getIntegerParam('id'));
        $this->response->redirect($this->helper->url->to('UserViewController', 'sessions', array('user_id' => $user['id'])));
    }

    /**
     * Display user notifications
     *
     * @access public
     */
    public function notifications()
    {
        $user = $this->getUser();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            $this->userNotification->saveSettings($user['id'], $values);
            $this->flash->success(t('User updated successfully.'));
            return $this->response->redirect($this->helper->url->to('UserViewController', 'notifications', array('user_id' => $user['id'])));
        }

        return $this->response->html($this->helper->layout->user('user_view/notifications', array(
            'projects'      => $this->projectUserRole->getProjectsByUser($user['id'], array(ProjectModel::ACTIVE)),
            'notifications' => $this->userNotification->readSettings($user['id']),
            'types'         => $this->userNotificationType->getTypes(),
            'filters'       => $this->userNotificationFilter->getFilters(),
            'user'          => $user,
        )));
    }

    /**
     * Display user integrations
     *
     * @access public
     */
    public function integrations()
    {
        $user = $this->getUser();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            $this->userMetadata->save($user['id'], $values);
            $this->flash->success(t('User updated successfully.'));
            $this->response->redirect($this->helper->url->to('UserViewController', 'integrations', array('user_id' => $user['id'])));
        }

        $this->response->html($this->helper->layout->user('user_view/integrations', array(
            'user'   => $user,
            'values' => $this->userMetadata->getAll($user['id']),
        )));
    }

    /**
     * Display external accounts
     *
     * @access public
     */
    public function external()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->user('user_view/external', array(
            'last_logins' => $this->lastLogin->getAll($user['id']),
            'user'        => $user,
        )));
    }

    /**
     * Public access management
     *
     * @access public
     */
    public function share()
    {
        $user = $this->getUser();
        $switch = $this->request->getStringParam('switch');

        if ($switch === 'enable' || $switch === 'disable') {
            $this->checkCSRFParam();

            if ($this->user->{$switch . 'PublicAccess'}($user['id'])) {
                $this->flash->success(t('User updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update this user.'));
            }

            return $this->response->redirect($this->helper->url->to('UserViewController', 'share', array('user_id' => $user['id'])));
        }

        return $this->response->html($this->helper->layout->user('user_view/share', array(
            'user'  => $user,
            'title' => t('Public access'),
        )));
    }
}
