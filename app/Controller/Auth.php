<?php

namespace Controller;

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
            $this->response->redirect($this->helper->url('app', 'index'));
        }

        $this->response->html($this->template->layout('auth/index', array(
            'errors' => $errors,
            'values' => $values,
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
                $this->response->redirect('?'.urldecode($redirect_query));
            }

            $this->response->redirect($this->helper->url('app', 'index'));
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
        $this->response->redirect($this->helper->url('auth', 'login'));
    }
}
