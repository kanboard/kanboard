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
        $this->step1('Google');
    }

    /**
     * Link or authenticate a Github account
     *
     * @access public
     */
    public function github()
    {
        $this->step1('Github');
    }

    /**
     * Link or authenticate a Gitlab account
     *
     * @access public
     */
    public function gitlab()
    {
        $this->step1('Gitlab');
    }

    /**
     * Unlink external account
     *
     * @access public
     */
    public function unlink()
    {
        $backend = $this->request->getStringParam('backend');
        $this->checkCSRFParam();

        if ($this->authenticationManager->getProvider($backend)->unlink($this->userSession->getId())) {
            $this->flash->success(t('Your external account is not linked anymore to your profile.'));
        } else {
            $this->flash->failure(t('Unable to unlink your external account.'));
        }

        $this->response->redirect($this->helper->url->to('user', 'external', array('user_id' => $this->userSession->getId())));
    }

    /**
     * Redirect to the provider if no code received
     *
     * @access private
     * @param string $provider
     */
    private function step1($provider)
    {
        $code = $this->request->getStringParam('code');

        if (! empty($code)) {
            $this->step2($provider, $code);
        } else {
            $this->response->redirect($this->authenticationManager->getProvider($provider)->getService()->getAuthorizationUrl());
        }
    }

    /**
     * Link or authenticate the user
     *
     * @access private
     * @param string $provider
     * @param string $code
     */
    private function step2($provider, $code)
    {
        $this->authenticationManager->getProvider($provider)->setCode($code);

        if ($this->userSession->isLogged()) {
            $this->link($provider);
        }

        $this->authenticate($provider);
    }

    /**
     * Link the account
     *
     * @access private
     * @param string $provider
     */
    private function link($provider)
    {
        $authProvider = $this->authenticationManager->getProvider($provider);

        if (! $authProvider->authenticate()) {
            $this->flash->failure(t('External authentication failed'));
        } else {
            $this->userProfile->assign($this->userSession->getId(), $authProvider->getUser());
            $this->flash->success(t('Your external account is linked to your profile successfully.'));
        }

        $this->response->redirect($this->helper->url->to('user', 'external', array('user_id' => $this->userSession->getId())));
    }

    /**
     * Authenticate the account
     *
     * @access private
     * @param string $provider
     */
    private function authenticate($provider)
    {
        if ($this->authenticationManager->oauthAuthentication($provider)) {
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
