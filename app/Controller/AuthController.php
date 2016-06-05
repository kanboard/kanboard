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
        $this->sessionStorage->hasRememberMe = ! empty($values['remember_me']);
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

    /**
     * Redirect the user after the authentication
     *
     * @access private
     */
    private function redirectAfterLogin()
    {
        if (isset($this->sessionStorage->redirectAfterLogin) && ! empty($this->sessionStorage->redirectAfterLogin) && ! filter_var($this->sessionStorage->redirectAfterLogin, FILTER_VALIDATE_URL)) {
            $redirect = $this->sessionStorage->redirectAfterLogin;
            unset($this->sessionStorage->redirectAfterLogin);
            $this->response->redirect($redirect);
        } else {
            $this->response->redirect($this->helper->url->to('DashboardController', 'show'));
        }
    }
}
