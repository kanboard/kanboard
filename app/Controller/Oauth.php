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
    protected function step1($provider)
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
     * @access protected
     * @param string $provider
     * @param string $code
     */
    protected function step2($provider, $code)
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
     * @access protected
     * @param string $provider
     */
    protected function link($provider)
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
     * @access protected
     * @param string $provider
     */
    protected function authenticate($provider)
    {
        if ($this->authenticationManager->oauthAuthentication($provider)) {
            $this->response->redirect($this->helper->url->to('app', 'index'));
        } else {
            $this->response->html($this->helper->layout->app('auth/index', array(
                'errors' => array('login' => t('External authentication failed')),
                'values' => array(),
                'no_layout' => true,
                'title' => t('Login')
            )));
        }
    }
}
