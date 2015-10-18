<?php

namespace Kanboard\Controller;

use Kanboard\Model\NotificationType;

/**
 * User controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class User extends Base
{
    /**
     * Common layout for user views
     *
     * @access protected
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    protected function layout($template, array $params)
    {
        $content = $this->template->render($template, $params);
        $params['user_content_for_layout'] = $content;
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->userSession->getId());

        if (isset($params['user'])) {
            $params['title'] = ($params['user']['name'] ?: $params['user']['username']).' (#'.$params['user']['id'].')';
        }

        return $this->template->layout('user/layout', $params);
    }

    /**
     * List all users
     *
     * @access public
     */
    public function index()
    {
        $paginator = $this->paginator
                ->setUrl('user', 'index')
                ->setMax(30)
                ->setOrder('username')
                ->setQuery($this->user->getQuery())
                ->calculate();

        $this->response->html(
            $this->template->layout('user/index', array(
                'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
                'title' => t('Users').' ('.$paginator->getTotal().')',
                'paginator' => $paginator,
        )));
    }

    /**
     * Display a form to create a new user
     *
     * @access public
     */
    public function create(array $values = array(), array $errors = array())
    {
        $is_remote = $this->request->getIntegerParam('remote') == 1 || (isset($values['is_ldap_user']) && $values['is_ldap_user'] == 1);

        $this->response->html($this->template->layout($is_remote ? 'user/create_remote' : 'user/create_local', array(
            'timezones' => $this->config->getTimezones(true),
            'languages' => $this->config->getLanguages(true),
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
            'projects' => $this->project->getList(),
            'errors' => $errors,
            'values' => $values,
            'title' => t('New user')
        )));
    }

    /**
     * Validate and save a new user
     *
     * @access public
     */
    public function save()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->user->validateCreation($values);

        if ($valid) {
            $project_id = empty($values['project_id']) ? 0 : $values['project_id'];
            unset($values['project_id']);

            $user_id = $this->user->create($values);

            if ($user_id !== false) {
                $this->projectPermission->addMember($project_id, $user_id);

                if (! empty($values['notifications_enabled'])) {
                    $this->userNotificationType->saveSelectedTypes($user_id, array(NotificationType::TYPE_EMAIL));
                }

                $this->session->flash(t('User created successfully.'));
                $this->response->redirect($this->helper->url->to('user', 'show', array('user_id' => $user_id)));
            } else {
                $this->session->flashError(t('Unable to create your user.'));
                $values['project_id'] = $project_id;
            }
        }

        $this->create($values, $errors);
    }

    /**
     * Display user information
     *
     * @access public
     */
    public function show()
    {
        $user = $this->getUser();
        $this->response->html($this->layout('user/show', array(
            'user' => $user,
            'timezones' => $this->config->getTimezones(true),
            'languages' => $this->config->getLanguages(true),
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
            ->setUrl('user', 'timesheet', array('user_id' => $user['id'], 'pagination' => 'subtasks'))
            ->setMax(20)
            ->setOrder('start')
            ->setDirection('DESC')
            ->setQuery($this->subtaskTimeTracking->getUserQuery($user['id']))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $this->response->html($this->layout('user/timesheet', array(
            'subtask_paginator' => $subtask_paginator,
            'user' => $user,
        )));
    }

    /**
     * Display last connections
     *
     * @access public
     */
    public function last()
    {
        $user = $this->getUser();
        $this->response->html($this->layout('user/last', array(
            'last_logins' => $this->lastLogin->getAll($user['id']),
            'user' => $user,
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
        $this->response->html($this->layout('user/sessions', array(
            'sessions' => $this->authentication->backend('rememberMe')->getAll($user['id']),
            'user' => $user,
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
        $this->authentication->backend('rememberMe')->remove($this->request->getIntegerParam('id'));
        $this->response->redirect($this->helper->url->to('user', 'session', array('user_id' => $user['id'])));
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
            $this->session->flash(t('User updated successfully.'));
            $this->response->redirect($this->helper->url->to('user', 'notifications', array('user_id' => $user['id'])));
        }

        $this->response->html($this->layout('user/notifications', array(
            'projects' => $this->projectPermission->getMemberProjects($user['id']),
            'notifications' => $this->userNotification->readSettings($user['id']),
            'types' => $this->userNotificationType->getTypes(),
            'filters' => $this->userNotificationFilter->getFilters(),
            'user' => $user,
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
            $this->session->flash(t('User updated successfully.'));
            $this->response->redirect($this->helper->url->to('user', 'integrations', array('user_id' => $user['id'])));
        }

        $this->response->html($this->layout('user/integrations', array(
            'user' => $user,
            'values' => $this->userMetadata->getall($user['id']),
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
        $this->response->html($this->layout('user/external', array(
            'last_logins' => $this->lastLogin->getAll($user['id']),
            'user' => $user,
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

            if ($this->user->{$switch.'PublicAccess'}($user['id'])) {
                $this->session->flash(t('User updated successfully.'));
            } else {
                $this->session->flashError(t('Unable to update this user.'));
            }

            $this->response->redirect($this->helper->url->to('user', 'share', array('user_id' => $user['id'])));
        }

        $this->response->html($this->layout('user/share', array(
            'user' => $user,
            'title' => t('Public access'),
        )));
    }

    /**
     * Password modification
     *
     * @access public
     */
    public function password()
    {
        $user = $this->getUser();
        $values = array('id' => $user['id']);
        $errors = array();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            list($valid, $errors) = $this->user->validatePasswordModification($values);

            if ($valid) {
                if ($this->user->update($values)) {
                    $this->session->flash(t('Password modified successfully.'));
                } else {
                    $this->session->flashError(t('Unable to change the password.'));
                }

                $this->response->redirect($this->helper->url->to('user', 'show', array('user_id' => $user['id'])));
            }
        }

        $this->response->html($this->layout('user/password', array(
            'values' => $values,
            'errors' => $errors,
            'user' => $user,
        )));
    }

    /**
     * Display a form to edit a user
     *
     * @access public
     */
    public function edit()
    {
        $user = $this->getUser();
        $values = $user;
        $errors = array();

        unset($values['password']);

        if ($this->request->isPost()) {
            $values = $this->request->getValues();

            if ($this->userSession->isAdmin()) {
                $values += array('is_admin' => 0, 'is_project_admin' => 0);
            } else {
                // Regular users can't be admin
                if (isset($values['is_admin'])) {
                    unset($values['is_admin']);
                }

                if (isset($values['is_project_admin'])) {
                    unset($values['is_project_admin']);
                }
            }

            list($valid, $errors) = $this->user->validateModification($values);

            if ($valid) {
                if ($this->user->update($values)) {
                    $this->session->flash(t('User updated successfully.'));
                } else {
                    $this->session->flashError(t('Unable to update your user.'));
                }

                $this->response->redirect($this->helper->url->to('user', 'show', array('user_id' => $user['id'])));
            }
        }

        $this->response->html($this->layout('user/edit', array(
            'values' => $values,
            'errors' => $errors,
            'user' => $user,
            'timezones' => $this->config->getTimezones(true),
            'languages' => $this->config->getLanguages(true),
        )));
    }

    /**
     * Display a form to edit authentication
     *
     * @access public
     */
    public function authentication()
    {
        $user = $this->getUser();
        $values = $user;
        $errors = array();

        unset($values['password']);

        if ($this->request->isPost()) {
            $values = $this->request->getValues() + array('disable_login_form' => 0, 'is_ldap_user' => 0);
            list($valid, $errors) = $this->user->validateModification($values);

            if ($valid) {
                if ($this->user->update($values)) {
                    $this->session->flash(t('User updated successfully.'));
                } else {
                    $this->session->flashError(t('Unable to update your user.'));
                }

                $this->response->redirect($this->helper->url->to('user', 'authentication', array('user_id' => $user['id'])));
            }
        }

        $this->response->html($this->layout('user/authentication', array(
            'values' => $values,
            'errors' => $errors,
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

        if ($this->request->getStringParam('confirmation') === 'yes') {
            $this->checkCSRFParam();

            if ($this->user->remove($user['id'])) {
                $this->session->flash(t('User removed successfully.'));
            } else {
                $this->session->flashError(t('Unable to remove this user.'));
            }

            $this->response->redirect($this->helper->url->to('user', 'index'));
        }

        $this->response->html($this->layout('user/remove', array(
            'user' => $user,
        )));
    }
}
