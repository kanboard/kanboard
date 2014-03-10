<?php

namespace Controller;

require_once __DIR__.'/Base.php';

class User extends Base
{
    // Display access forbidden page
    public function forbidden()
    {
        $this->response->html($this->template->layout('user_forbidden', array(
            'menu' => 'users',
            'title' => t('Access Forbidden')
        )));
    }

    // Logout and destroy session
    public function logout()
    {
        $this->session->close();
        $this->response->redirect('?controller=user&action=login');
    }

    // Display the form login
    public function login()
    {
        if (isset($_SESSION['user'])) $this->response->redirect('?controller=app');

        $this->response->html($this->template->layout('user_login', array(
            'errors' => array(),
            'values' => array(),
            'no_layout' => true,
            'title' => t('Login')
        )));
    }

    // Check credentials
    public function check()
    {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->user->validateLogin($values);

        if ($valid) $this->response->redirect('?controller=app');

        $this->response->html($this->template->layout('user_login', array(
            'errors' => $errors,
            'values' => $values,
            'no_layout' => true,
            'title' => t('Login')
        )));
    }

    // List all users
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

    // Display a form to create a new user
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

    // Validate and save a new user
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

    // Display a form to edit a user
    public function edit()
    {
        $user = $this->user->getById($this->request->getIntegerParam('user_id'));

        if (! $user) $this->notfound();

        if (! $_SESSION['user']['is_admin'] && $_SESSION['user']['id'] != $user['id']) {
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

    // Validate and update a user
    public function update()
    {
        $values = $this->request->getValues();

        if ($_SESSION['user']['is_admin'] == 1) {
            $values += array('is_admin' => 0);
        }
        else {

            if ($_SESSION['user']['id'] != $values['id']) {
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

    // Confirmation dialog before to remove a user
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

    // Remove a user
    public function remove()
    {
        $user_id = $this->request->getIntegerParam('user_id');

        if ($user_id && $this->user->remove($user_id)) {
            $this->session->flash(t('User removed successfully.'));
        } else {
            $this->session->flashError(t('Unable to remove this user.'));
        }

        $this->response->redirect('?controller=user');
    }
}
