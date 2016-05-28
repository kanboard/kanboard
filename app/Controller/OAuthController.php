<?php

namespace Kanboard\Controller;

use Kanboard\Core\Security\OAuthAuthenticationProviderInterface;

/**
 * OAuth Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class OAuthController extends BaseController
{
    /**
     * Redirect to the provider if no code received
     *
     * @access private
     * @param string $provider
     */
    protected function step1($provider)
    {
        $code = $this->request->getStringParam('code');
        $state = $this->request->getStringParam('state');

        if (! empty($code)) {
            $this->step2($provider, $code, $state);
        } else {
            $this->response->redirect($this->authenticationManager->getProvider($provider)->getService()->getAuthorizationUrl());
        }
    }

    /**
     * Link or authenticate the user
     *
     * @access protected
     * @param string $providerName
     * @param string $code
     * @param string $state
     */
    protected function step2($providerName, $code, $state)
    {
        $provider = $this->authenticationManager->getProvider($providerName);
        $provider->setCode($code);
        $hasValidState = $provider->getService()->isValidateState($state);

        if ($this->userSession->isLogged()) {
            if ($hasValidState) {
                $this->link($provider);
            } else {
                $this->flash->failure(t('The OAuth2 state parameter is invalid'));
                $this->response->redirect($this->helper->url->to('UserViewController', 'external', array('user_id' => $this->userSession->getId())));
            }
        } else {
            if ($hasValidState) {
                $this->authenticate($providerName);
            } else {
                $this->authenticationFailure(t('The OAuth2 state parameter is invalid'));
            }
        }
    }

    /**
     * Link the account
     *
     * @access protected
     * @param  OAuthAuthenticationProviderInterface $provider
     */
    protected function link(OAuthAuthenticationProviderInterface $provider)
    {
        if (! $provider->authenticate()) {
            $this->flash->failure(t('External authentication failed'));
        } else {
            $this->userProfile->assign($this->userSession->getId(), $provider->getUser());
            $this->flash->success(t('Your external account is linked to your profile successfully.'));
        }

        $this->response->redirect($this->helper->url->to('UserViewController', 'external', array('user_id' => $this->userSession->getId())));
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

        $this->response->redirect($this->helper->url->to('UserViewController', 'external', array('user_id' => $this->userSession->getId())));
    }

    /**
     * Authenticate the account
     *
     * @access protected
     * @param string $providerName
     */
    protected function authenticate($providerName)
    {
        if ($this->authenticationManager->oauthAuthentication($providerName)) {
            $this->response->redirect($this->helper->url->to('DashboardController', 'show'));
        } else {
            $this->authenticationFailure(t('External authentication failed'));
        }
    }

    /**
     * Show login failure page
     *
     * @access protected
     * @param  string $message
     */
    protected function authenticationFailure($message)
    {
        $this->response->html($this->helper->layout->app('auth/index', array(
            'errors' => array('login' => $message),
            'values' => array(),
            'no_layout' => true,
            'title' => t('Login')
        )));
    }
}
