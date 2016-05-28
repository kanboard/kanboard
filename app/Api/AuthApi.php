<?php

namespace Kanboard\Api;

use JsonRPC\Exception\AuthenticationFailureException;

/**
 * Base class
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class AuthApi extends BaseApi
{
    /**
     * Check api credentials
     *
     * @access public
     * @param  string  $username
     * @param  string  $password
     * @param  string  $class
     * @param  string  $method
     * @throws AuthenticationFailureException
     */
    public function checkCredentials($username, $password, $class, $method)
    {
        $this->dispatcher->dispatch('app.bootstrap');

        if ($this->isUserAuthenticated($username, $password)) {
            $this->checkProcedurePermission(true, $method);
            $this->userSession->initialize($this->userModel->getByUsername($username));
        } elseif ($this->isAppAuthenticated($username, $password)) {
            $this->checkProcedurePermission(false, $method);
        } else {
            $this->logger->error('API authentication failure for '.$username);
            throw new AuthenticationFailureException('Wrong credentials');
        }
    }

    /**
     * Check user credentials
     *
     * @access public
     * @param  string  $username
     * @param  string  $password
     * @return boolean
     */
    private function isUserAuthenticated($username, $password)
    {
        return $username !== 'jsonrpc' &&
            ! $this->userLockingModel->isLocked($username) &&
            $this->authenticationManager->passwordAuthentication($username, $password);
    }

    /**
     * Check administrative credentials
     *
     * @access public
     * @param  string  $username
     * @param  string  $password
     * @return boolean
     */
    private function isAppAuthenticated($username, $password)
    {
        return $username === 'jsonrpc' && $password === $this->getApiToken();
    }

    /**
     * Get API Token
     *
     * @access private
     * @return string
     */
    private function getApiToken()
    {
        if (defined('API_AUTHENTICATION_TOKEN')) {
            return API_AUTHENTICATION_TOKEN;
        }

        return $this->configModel->get('api_token');
    }
}
