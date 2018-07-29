<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Password Reset Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class PasswordResetController extends BaseController
{
    /**
     * Show the form to reset the password
     *
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\BaseException
     */
    public function create(array $values = array(), array $errors = array())
    {
        $this->checkActivation();

        $this->response->html($this->helper->layout->app('password_reset/create', array(
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
            $this->response->redirect($this->helper->url->to('AuthController', 'login'));
        } else {
            $this->create($values, $errors);
        }
    }

    /**
     * Show the form to set a new password
     *
     * @param array $values
     * @param array $errors
     * @throws \Kanboard\Core\Controller\BaseException
     */
    public function change(array $values = array(), array $errors = array())
    {
        $this->checkActivation();

        $token = $this->request->getStringParam('token');
        $user_id = $this->passwordResetModel->getUserIdByToken($token);

        if ($user_id !== false) {
            $this->response->html($this->helper->layout->app('password_reset/change', array(
                'token' => $token,
                'errors' => $errors,
                'values' => $values,
                'no_layout' => true,
            )));
        } else {
            $this->response->redirect($this->helper->url->to('AuthController', 'login'));
        }
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
            $user_id = $this->passwordResetModel->getUserIdByToken($token);

            if ($user_id !== false) {
                $this->userModel->update(array('id' => $user_id, 'password' => $values['password']));
                $this->passwordResetModel->disable($user_id);
            }

            return $this->response->redirect($this->helper->url->to('AuthController', 'login'));
        }

        return $this->change($values, $errors);
    }

    /**
     * Send the email
     *
     * @param string $username
     */
    protected function sendEmail($username)
    {
        $token = $this->passwordResetModel->create($username);

        if ($token !== false) {
            $user = $this->userCacheDecorator->getByUsername($username);

            $this->emailClient->send(
                $user['email'],
                $user['name'] ?: $user['username'],
                t('Password Reset for Kanboard'),
                $this->template->render('password_reset/email', array('token' => $token))
            );

            $this->flash->success(t('A link to reset your password has been sent by email.'));
        } else {
            $this->flash->failure(t('Unfortunately, we are unable to reset your password. Did you enter a valid username? Do you have an email address in your profile?'));
        }
    }

    /**
     * Check feature availability
     */
    protected function checkActivation()
    {
        if ($this->configModel->get('password_reset', 0) == 0) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }
    }
}
