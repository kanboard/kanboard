<?php

namespace Kanboard\Api;

use JsonRPC\AuthenticationFailure;
use Symfony\Component\EventDispatcher\Event;

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
        $this->container['dispatcher']->dispatch('api.bootstrap', new Event);

        if ($username !== 'jsonrpc' && ! $this->authentication->hasCaptcha($username) && $this->authentication->authenticate($username, $password)) {
            $this->checkProcedurePermission(true, $method);
            $this->userSession->refresh($this->user->getByUsername($username));
        } elseif ($username === 'jsonrpc' && $password === $this->config->get('api_token')) {
            $this->checkProcedurePermission(false, $method);
        } else {
            throw new AuthenticationFailure('Wrong credentials');
        }
    }
}
