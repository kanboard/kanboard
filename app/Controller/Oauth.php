<?php

namespace Kanboard\Controller;

/**
 * OAuth controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Oauth extends Base
{
    /**
     * Link or authenticate a Google account
     *
     * @access public
     */
    public function google()
    {
        $this->step1('google');
    }

    /**
     * Link or authenticate a Github account
     *
     * @access public
     */
    public function github()
    {
        $this->step1('github');
    }

    /**
     * Link or authenticate a Gitlab account
     *
     * @access public
     */
    public function gitlab()
    {
        $this->step1('gitlab');
    }

    /**
     * Unlink external account
     *
     * @access public
     */
    public function unlink($backend = '')
    {
        $backend = $this->request->getStringParam('backend', $backend);
        $this->checkCSRFParam();

        if ($this->authentication->backend($backend)->unlink($this->userSession->getId())) {
            $this->session->flash(t('Your external account is not linked anymore to your profile.'));
        } else {
            $this->session->flashError(t('Unable to unlink your external account.'));
        }

        $this->response->redirect($this->helper->url->to('user', 'external', array('user_id' => $this->userSession->getId())));
    }

    /**
     * Redirect to the provider if no code received
     *
     * @access private
     */
    private function step1($backend)
    {
        $code = $this->request->getStringParam('code');

        if (! empty($code)) {
            $this->step2($backend, $code);
        } else {
            $this->response->redirect($this->authentication->backend($backend)->getService()->getAuthorizationUrl());
        }
    }

    /**
     * Link or authenticate the user
     *
     * @access private
     */
    private function step2($backend, $code)
    {
        $profile = $this->authentication->backend($backend)->getProfile($code);

        if ($this->userSession->isLogged()) {
            $this->link($backend, $profile);
        }

        $this->authenticate($backend, $profile);
    }

    /**
     * Link the account
     *
     * @access private
     */
    private function link($backend, $profile)
    {
        if (empty($profile)) {
            $this->session->flashError(t('External authentication failed'));
        } else {
            $this->session->flash(t('Your external account is linked to your profile successfully.'));
            $this->authentication->backend($backend)->updateUser($this->userSession->getId(), $profile);
        }

        $this->response->redirect($this->helper->url->to('user', 'external', array('user_id' => $this->userSession->getId())));
    }

    /**
     * Authenticate the account
     *
     * @access private
     */
    private function authenticate($backend, $profile)
    {
        if (! empty($profile) && $this->authentication->backend($backend)->authenticate($profile['id'])) {
            $this->response->redirect($this->helper->url->to('app', 'index'));
        } else {
            $this->response->html($this->template->layout('auth/index', array(
                'errors' => array('login' => t('External authentication failed')),
                'values' => array(),
                'no_layout' => true,
                'title' => t('Login')
            )));
        }
    }
}
