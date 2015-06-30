<?php

namespace Api;

use JsonRPC\AuthenticationFailure;
use Symfony\Component\EventDispatcher\Event;

/**
 * Base class
 *
 * @package  api
 * @author   Frederic Guillot
 */
abstract class Base extends \Core\Base
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
    public function authentication($username, $password, $class, $method)
    {
        $this->container['dispatcher']->dispatch('api.bootstrap', new Event);

        if (! ($username === 'jsonrpc' && $password === $this->config->get('api_token'))) {
            throw new AuthenticationFailure('Wrong credentials');
        }
    }
}
