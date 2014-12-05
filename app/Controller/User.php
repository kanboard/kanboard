<?php

namespace Controller;

/**
 * User controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class User extends Base
{
    /**
     * Logout and destroy session
     *
     * @access public
     */
    public function logout()
    {
        $this->checkCSRFParam();
        $this->authentication->backend('rememberMe')->destroy($this->acl->getUserId());
        $this->session->close();
        $this->response->redirect('?controller=user&action=login');
    }

    /**
     * Display the form login
     *
     * @access public
     */
    public function login()
    {
        if ($this->acl->isLogged()) {
            $this->response->redirect('?controller=app');
        }

        $this->response->html($this->template->layout('user/login', array(
            'errors' => array(),
            'values' => array(),
            'no_layout' => true,
            'redirect_query' => $this->request->getStringParam('redirect_query'),
            'title' => t('Login')
        )));
    }

    /**
     * Check credentials
     *
     * @access public
     */
    public function check()
    {
        $redirect_query = $this->request->getStringParam('redirect_query');
        $values = $this->request->getValues();
        list($valid, $errors) = $this->authentication->validateForm($values);

        if ($valid) {
            if ($redirect_query !== '') {
                $this->response->redirect('?'.$redirect_query);
            }
            else {
                $this->response->redirect('?controller=app');
            }
        }

        $this->response->html($this->template->layout('user/login', array(
            'errors' => $errors,
            'values' => $values,
            'no_layout' => true,
            'redirect_query' => $redirect_query,
            'title' => t('Login')
        )));
    }

    /**
     * Common layout for user views
     *
     * @access private
     * @param  string    $template   Template name
     * @param  array     $params     Template parameters
     * @return string
     */
    private function layout($template, array $params)
    {
        $content = $this->template->load($template, $params);
        $params['user_content_for_layout'] = $content;
        $params['board_selector'] = $this->projectPermission->getAllowedProjects($this->acl->getUserId());

        if (isset($params['user'])) {
            $params['title'] = ($params['user']['name'] ?: $params['user']['username']).' (#'.$params['user']['id'].')';
        }

        return $this->template->layout('user/layout', $params);
    }

    /**
     * Common method to get the user
     *
     * @access private
     * @return array
     */
    private function getUser()
    {
        $user = $this->user->getById($this->request->getIntegerParam('user_id'));

        if (! $user) {
            $this->notfound();
        }

        if ($this->acl->isRegularUser() && $this->acl->getUserId() != $user['id']) {
            $this->forbidden();
        }

        return $user;
    }

    /**
     * List all users
     *
     * @access public
     */
    public function index()
    {
        $direction = $this->request->getStringParam('direction', 'ASC');
        $order = $this->request->getStringParam('order', 'username');
        $offset = $this->request->getIntegerParam('offset', 0);
        $limit = 25;

        $users = $this->user->paginate($offset, $limit, $order, $direction);
        $nb_users = $this->user->count();

        $this->response->html(
            $this->template->layout('user/index', array(
                'board_selector' => $this->projectPermission->getAllowedProjects($this->acl->getUserId()),
                'projects' => $this->project->getList(),
                'nb_users' => $nb_users,
                'users' => $users,
                'title' => t('Users').' ('.$nb_users.')',
                'pagination' => array(
                    'controller' => 'user',
                    'action' => 'index',
                    'direction' => $direction,
                    'order' => $order,
                    'total' => $nb_users,
                    'offset' => $offset,
                    'limit' => $limit,
                    'params' => array(),
                ),
        )));
    }

    /**
     * Display a form to create a new user
     *
     * @access public
     */
    public function create()
    {
        $this->response->html($this->template->layout('user/new', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->acl->getUserId()),
            'projects' => $this->project->getList(),
            'errors' => array(),
            'values' => array(),
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

            if ($this->user->create($values)) {
                $this->session->flash(t('User created successfully.'));
                $this->response->redirect('?controller=user');
            }
            else {
                $this->session->flashError(t('Unable to create your user.'));
            }
        }

        $this->response->html($this->template->layout('user/new', array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->acl->getUserId()),
            'projects' => $this->project->getList(),
            'errors' => $errors,
            'values' => $values,
            'title' => t('New user')
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
        $this->response->html($this->layout('user/show', array(
            'projects' => $this->projectPermission->getAllowedProjects($user['id']),
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
        $this->response->redirect('?controller=user&action=sessions&user_id='.$user['id']);
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
            $this->notification->saveSettings($user['id'], $values);
            $this->session->flash(t('User updated successfully.'));
            $this->response->redirect('?controller=user&action=notifications&user_id='.$user['id']);
        }

        $this->response->html($this->layout('user/notifications', array(
            'projects' => $this->projectPermission->getAllowedProjects($user['id']),
            'notifications' => $this->notification->readSettings($user['id']),
            'user' => $user,
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
                }
                else {
                    $this->session->flashError(t('Unable to change the password.'));
                }

                $this->response->redirect('?controller=user&action=show&user_id='.$user['id']);
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

            if ($this->acl->isAdminUser()) {
                $values += array('is_admin' => 0);
            }
            else {

                if (isset($values['is_admin'])) {
                    unset($values['is_admin']); // Regular users can't be admin
                }
            }

            list($valid, $errors) = $this->user->validateModification($values);

            if ($valid) {

                if ($this->user->update($values)) {
                    $this->session->flash(t('User updated successfully.'));
                }
                else {
                    $this->session->flashError(t('Unable to update your user.'));
                }

                $this->response->redirect('?controller=user&action=show&user_id='.$user['id']);
            }
        }

        $this->response->html($this->layout('user/edit', array(
            'values' => $values,
            'errors' => $errors,
            'projects' => $this->projectPermission->filterProjects($this->project->getList(), $user['id']),
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

            $this->response->redirect('?controller=user');
        }

        $this->response->html($this->layout('user/remove', array(
            'user' => $user,
        )));
    }

    /**
     * Google authentication
     *
     * @access public
     */
    public function google()
    {
        $code = $this->request->getStringParam('code');

        if ($code) {

            $profile = $this->authentication->backend('google')->getGoogleProfile($code);

            if (is_array($profile)) {

                // If the user is already logged, link the account otherwise authenticate
                if ($this->acl->isLogged()) {

                    if ($this->authentication->backend('google')->updateUser($this->acl->getUserId(), $profile)) {
                        $this->session->flash(t('Your Google Account is linked to your profile successfully.'));
                    }
                    else {
                        $this->session->flashError(t('Unable to link your Google Account.'));
                    }

                    $this->response->redirect('?controller=user&action=external&user_id='.$this->acl->getUserId());
                }
                else if ($this->authentication->backend('google')->authenticate($profile['id'])) {
                    $this->response->redirect('?controller=app');
                }
                else {
                    $this->response->html($this->template->layout('user/login', array(
                        'errors' => array('login' => t('Google authentication failed')),
                        'values' => array(),
                        'no_layout' => true,
                        'redirect_query' => '',
                        'title' => t('Login')
                    )));
                }
            }
        }

        $this->response->redirect($this->authentication->backend('google')->getAuthorizationUrl());
    }

    /**
     * Unlink a Google account
     *
     * @access public
     */
    public function unlinkGoogle()
    {
        $this->checkCSRFParam();
        if ($this->authentication->backend('google')->unlink($this->acl->getUserId())) {
            $this->session->flash(t('Your Google Account is not linked anymore to your profile.'));
        }
        else {
            $this->session->flashError(t('Unable to unlink your Google Account.'));
        }

        $this->response->redirect('?controller=user&action=external&user_id='.$this->acl->getUserId());
    }

    /**
     * GitHub authentication
     *
     * @access public
     */
    public function gitHub()
    {
        $code = $this->request->getStringParam('code');

        if ($code) {
            $profile = $this->authentication->backend('gitHub')->getGitHubProfile($code);

            if (is_array($profile)) {

                // If the user is already logged, link the account otherwise authenticate
                if ($this->acl->isLogged()) {

                    if ($this->authentication->backend('gitHub')->updateUser($this->acl->getUserId(), $profile)) {
                        $this->session->flash(t('Your GitHub account was successfully linked to your profile.'));
                    }
                    else {
                        $this->session->flashError(t('Unable to link your GitHub Account.'));
                    }

                    $this->response->redirect('?controller=user&action=external&user_id='.$this->acl->getUserId());
                }
                else if ($this->authentication->backend('gitHub')->authenticate($profile['id'])) {
                    $this->response->redirect('?controller=app');
                }
                else {
                    $this->response->html($this->template->layout('user/login', array(
                        'errors' => array('login' => t('GitHub authentication failed')),
                        'values' => array(),
                        'no_layout' => true,
                        'redirect_query' => '',
                        'title' => t('Login')
                    )));
                }
            }
        }

        $this->response->redirect($this->authentication->backend('gitHub')->getAuthorizationUrl());
    }

    /**
     * Unlink a GitHub account
     *
     * @access public
     */
    public function unlinkGitHub()
    {
        $this->checkCSRFParam();

        $this->authentication->backend('gitHub')->revokeGitHubAccess();

        if ($this->authentication->backend('gitHub')->unlink($this->acl->getUserId())) {
            $this->session->flash(t('Your GitHub account is no longer linked to your profile.'));
        }
        else {
            $this->session->flashError(t('Unable to unlink your GitHub Account.'));
        }

        $this->response->redirect('?controller=user&action=external&user_id='.$this->acl->getUserId());
    }
}
