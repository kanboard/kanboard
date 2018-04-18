<?php

namespace Kanboard\Controller;

/**
 * Authentication Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class AuthController extends BaseController
{
    /**
     * Display the form login
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function login(array $values = array(), array $errors = array())
    {
        if ($this->userSession->isLogged()) {
            $this->response->redirect($this->helper->url->to('DashboardController', 'show'));
        } else {
            $this->response->html($this->helper->layout->app('auth/index', array(
                'captcha' => ! empty($values['username']) && $this->userLockingModel->hasCaptcha($values['username']),
                'errors' => $errors,
                'values' => $values,
                'no_layout' => true,
                'title' => t('Login')
            )));
        }
    }

    /**
     * Check credentials
     *
     * @access public
     */
    public function check()
    {
        $values = $this->request->getValues();
        session_set('hasRememberMe', ! empty($values['remember_me']));
        list($valid, $errors) = $this->authValidator->validateForm($values);

        if ($valid) {
            $this->redirectAfterLogin();
        } else {
            $this->login($values, $errors);
        }
    }

    /**
     * Logout and destroy session
     *
     * @access public
     */
    public function logout()
    {
        if (! DISABLE_LOGOUT) {
            $this->sessionManager->close();
            $this->response->redirect($this->helper->url->to('AuthController', 'login'));
        } else {
            $this->response->redirect($this->helper->url->to('DashboardController', 'show'));
        }
    }
}
