<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\PageNotFoundException;
use Kanboard\Model\ProjectModel;

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
        $user = $this->userModel->getById($this->request->getIntegerParam('user_id'));

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
            'timezones' => $this->timezoneModel->getTimezones(true),
            'languages' => $this->languageModel->getLanguages(true),
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
            ->setQuery($this->subtaskTimeTrackingModel->getUserQuery($user['id']))
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
            'tokens' => $this->passwordResetModel->getAll($user['id']),
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
            'last_logins' => $this->lastLoginModel->getAll($user['id']),
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
            'sessions' => $this->rememberMeSessionModel->getAll($user['id']),
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
        $this->rememberMeSessionModel->remove($this->request->getIntegerParam('id'));

        if ($this->request->isAjax()) {
            $this->sessions();
        } else {
            $this->response->redirect($this->helper->url->to('UserViewController', 'sessions', array('user_id' => $user['id'])), true);
        }
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
            $this->userNotificationModel->saveSettings($user['id'], $values);
            $this->flash->success(t('User updated successfully.'));
            $this->response->redirect($this->helper->url->to('UserViewController', 'notifications', array('user_id' => $user['id'])), true);
            return;
        }

        $this->response->html($this->helper->layout->user('user_view/notifications', array(
            'projects'      => $this->projectUserRoleModel->getProjectsByUser($user['id'], array(ProjectModel::ACTIVE)),
            'notifications' => $this->userNotificationModel->readSettings($user['id']),
            'types'         => $this->userNotificationTypeModel->getTypes(),
            'filters'       => $this->userNotificationFilterModel->getFilters(),
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
            $this->userMetadataModel->save($user['id'], $values);
            $this->flash->success(t('User updated successfully.'));
            $this->response->redirect($this->helper->url->to('UserViewController', 'integrations', array('user_id' => $user['id'])), true);
            return;
        }

        $this->response->html($this->helper->layout->user('user_view/integrations', array(
            'user'   => $user,
            'values' => $this->userMetadataModel->getAll($user['id']),
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
            'last_logins' => $this->lastLoginModel->getAll($user['id']),
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

            if ($this->userModel->{$switch . 'PublicAccess'}($user['id'])) {
                $this->flash->success(t('User updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update this user.'));
            }

            if (! $this->request->isAjax()) {
                $this->response->redirect($this->helper->url->to('UserViewController', 'share', array('user_id' => $user['id'])), true);
                return;
            }

            $user = $this->getUser();
        }

        $this->response->html($this->helper->layout->user('user_view/share', array(
            'user'  => $user,
            'title' => t('Public access'),
        )));
    }
}
