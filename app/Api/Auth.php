<?php

namespace Kanboard\Api;

use JsonRPC\AuthenticationFailure;

/**
 * Base class
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Auth extends Base
{
    /**
     * Check api credentials
     *
     * @access public
     * @param  string  $username
     * @param  string  $password
     * @param  string  $class
     * @param  string  $method
     */
    public function checkCredentials($username, $password, $class, $method)
    {
        $this->dispatcher->dispatch('app.bootstrap');

        if ($this->isUserAuthenticated($username, $password)) {
            $this->checkProcedurePermission(true, $method);
            $this->userSession->initialize($this->user->getByUsername($username));
        } elseif ($this->isAppAuthenticated($username, $password)) {
            $this->checkProcedurePermission(false, $method);
        } else {
            $this->logger->error('API authentication failure for '.$username);
            throw new AuthenticationFailure('Wrong credentials');
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
            ! $this->userLocking->isLocked($username) &&
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

        return $this->config->get('api_token');
    }
}
