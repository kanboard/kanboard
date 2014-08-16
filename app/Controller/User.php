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

        $this->response->html($this->template->layout('user_login', array(
            'errors' => array(),
            'values' => array(),
            'no_layout' => true,
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
        $values = $this->request->getValues();
        list($valid, $errors) = $this->authentication->validateForm($values);

        if ($valid) {
            $this->response->redirect('?controller=app');
        }

        $this->response->html($this->template->layout('user_login', array(
            'errors' => $errors,
            'values' => $values,
            'no_layout' => true,
            'title' => t('Login')
        )));
    }

    /**
     * List all users
     *
     * @access public
     */
    public function index()
    {
        $users = $this->user->getAll();
        $nb_users = count($users);

        $this->response->html(
            $this->template->layout('user_index', array(
                'projects' => $this->project->getList(),
                'users' => $users,
                'nb_users' => $nb_users,
                'menu' => 'users',
                'title' => t('Users').' ('.$nb_users.')'
        )));
    }

    /**
     * Display a form to create a new user
     *
     * @access public
     */
    public function create()
    {
        $this->response->html($this->template->layout('user_new', array(
            'projects' => $this->project->getList(),
            'errors' => array(),
            'values' => array(),
            'menu' => 'users',
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

        $this->response->html($this->template->layout('user_new', array(
            'projects' => $this->project->getList(),
            'errors' => $errors,
            'values' => $values,
            'menu' => 'users',
            'title' => t('New user')
        )));
    }

    /**
     * Display a form to edit a user
     *
     * @access public
     */
    public function edit()
    {
        $user = $this->user->getById($this->request->getIntegerParam('user_id'));

        if (! $user) $this->notfound();

        if ($this->acl->isRegularUser() && $this->acl->getUserId() != $user['id']) {
            $this->forbidden();
        }

        unset($user['password']);

        $this->response->html($this->template->layout('user_edit', array(
            'projects' => $this->project->filterListByAccess($this->project->getList(), $user['id']),
            'errors' => array(),
            'values' => $user,
            'menu' => 'users',
            'title' => t('Edit user')
        )));
    }

    /**
     * Validate and update a user
     *
     * @access public
     */
    public function update()
    {
        $values = $this->request->getValues();

        if ($this->acl->isAdminUser()) {
            $values += array('is_admin' => 0);
        }
        else {

            if ($this->acl->getUserId() != $values['id']) {
                $this->forbidden();
            }

            if (isset($values['is_admin'])) {
                unset($values['is_admin']); // Regular users can't be admin
            }
        }

        list($valid, $errors) = $this->user->validateModification($values);

        if ($valid) {

            if ($this->user->update($values)) {
                $this->session->flash(t('User updated successfully.'));
                $this->response->redirect('?controller=user');
            }
            else {
                $this->session->flashError(t('Unable to update your user.'));
            }
        }

        $this->response->html($this->template->layout('user_edit', array(
            'projects' => $this->project->filterListByAccess($this->project->getList(), $values['id']),
            'errors' => $errors,
            'values' => $values,
            'menu' => 'users',
            'title' => t('Edit user')
        )));
    }

    /**
     * Confirmation dialog before to remove a user
     *
     * @access public
     */
    public function confirm()
    {
        $user = $this->user->getById($this->request->getIntegerParam('user_id'));

        if (! $user) $this->notfound();

        $this->response->html($this->template->layout('user_remove', array(
            'user' => $user,
            'menu' => 'users',
            'title' => t('Remove user')
        )));
    }

    /**
     * Remove a user
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $user_id = $this->request->getIntegerParam('user_id');

        if ($user_id && $this->user->remove($user_id)) {
            $this->session->flash(t('User removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this user.'));
        }

        $this->response->redirect('?controller=user');
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

                    $this->response->redirect('?controller=user');
                }
                else if ($this->authentication->backend('google')->authenticate($profile['id'])) {
                    $this->response->redirect('?controller=app');
                }
                else {
                    $this->response->html($this->template->layout('user_login', array(
                        'errors' => array('login' => t('Google authentication failed')),
                        'values' => array(),
                        'no_layout' => true,
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

        $this->response->redirect('?controller=user');
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

                    $this->response->redirect('?controller=user');
                }
                else if ($this->authentication->backend('gitHub')->authenticate($profile['id'])) {
                    $this->response->redirect('?controller=app');
                }
                else {
                    $this->response->html($this->template->layout('user_login', array(
                        'errors' => array('login' => t('GitHub authentication failed')),
                        'values' => array(),
                        'no_layout' => true,
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

        $this->response->redirect('?controller=user');
    }
}
