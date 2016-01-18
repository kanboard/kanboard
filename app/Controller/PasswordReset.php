<?php

namespace Kanboard\Controller;

/**
 * Password Reset Controller
 *
 * @package controller
 * @author  Frederic Guillot
 */
class PasswordReset extends Base
{
    /**
     * Show the form to reset the password
     */
    public function create(array $values = array(), array $errors = array())
    {
        $this->checkActivation();

        $this->response->html($this->template->layout('password_reset/create', array(
            'errors' => $errors,
            'values' => $values,
            'no_layout' => true,
        )));
    }

    /**
     * Validate and send the email
     */
    public function save()
    {
        $this->checkActivation();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->passwordResetValidator->validateCreation($values);

        if ($valid) {
            $this->sendEmail($values['username']);
            $this->response->redirect($this->helper->url->to('auth', 'login'));
        }

        $this->create($values, $errors);
    }

    /**
     * Show the form to set a new password
     */
    public function change(array $values = array(), array $errors = array())
    {
        $this->checkActivation();

        $token = $this->request->getStringParam('token');
        $user_id = $this->passwordReset->getUserIdByToken($token);

        if ($user_id !== false) {
            $this->response->html($this->template->layout('password_reset/change', array(
                'token' => $token,
                'errors' => $errors,
                'values' => $values,
                'no_layout' => true,
            )));
        }

        $this->response->redirect($this->helper->url->to('auth', 'login'));
    }

    /**
     * Set the new password
     */
    public function update()
    {
        $this->checkActivation();

        $token = $this->request->getStringParam('token');
        $values = $this->request->getValues();
        list($valid, $errors) = $this->passwordResetValidator->validateModification($values);

        if ($valid) {
            $user_id = $this->passwordReset->getUserIdByToken($token);

            if ($user_id !== false) {
                $this->user->update(array('id' => $user_id, 'password' => $values['password']));
                $this->passwordReset->disable($user_id);
            }

            $this->response->redirect($this->helper->url->to('auth', 'login'));
        }

        $this->change($values, $errors);
    }

    /**
     * Send the email
     */
    private function sendEmail($username)
    {
        $token = $this->passwordReset->create($username);

        if ($token !== false) {
            $user = $this->user->getByUsername($username);

            $this->emailClient->send(
                $user['email'],
                $user['name'] ?: $user['username'],
                t('Password Reset for Kanboard'),
                $this->template->render('password_reset/email', array('token' => $token))
            );
        }
    }

    /**
     * Check feature availability
     */
    private function checkActivation()
    {
        if ($this->config->get('password_reset', 0) == 0) {
            $this->response->redirect($this->helper->url->to('auth', 'login'));
        }
    }
}
