<?php

namespace Kanboard\Controller;

use Gregwar\Captcha\CaptchaBuilder;

/**
 * Authentication controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Auth extends Base
{
    /**
     * Display the form login
     *
     * @access public
     */
    public function login(array $values = array(), array $errors = array())
    {
        if ($this->userSession->isLogged()) {
            $this->response->redirect($this->helper->url->to('app', 'index'));
        }

        $this->response->html($this->template->layout('auth/index', array(
            'captcha' => isset($values['username']) && $this->authentication->hasCaptcha($values['username']),
            'errors' => $errors,
            'values' => $values,
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
            if (! empty($this->session['login_redirect']) && ! filter_var($this->session['login_redirect'], FILTER_VALIDATE_URL)) {
                $redirect = $this->session['login_redirect'];
                unset($this->session['login_redirect']);
                $this->response->redirect($redirect);
            }

            $this->response->redirect($this->helper->url->to('app', 'index'));
        }

        $this->login($values, $errors);
    }

    /**
     * Logout and destroy session
     *
     * @access public
     */
    public function logout()
    {
        $this->authentication->backend('rememberMe')->destroy($this->userSession->getId());
        $this->session->close();
        $this->response->redirect($this->helper->url->to('auth', 'login'));
    }

    /**
     * Display captcha image
     *
     * @access public
     */
    public function captcha()
    {
        $this->response->contentType('image/jpeg');

        $builder = new CaptchaBuilder;
        $builder->build();
        $this->session['captcha'] = $builder->getPhrase();
        $builder->output();
    }
}
