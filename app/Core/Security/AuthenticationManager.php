<?php

namespace Kanboard\Core\Security;

use LogicException;
use Kanboard\Core\Base;
use Kanboard\Event\AuthFailureEvent;
use Kanboard\Event\AuthSuccessEvent;

/**
 * Authentication Manager
 *
 * @package  security
 * @author   Frederic Guillot
 */
class AuthenticationManager extends Base
{
    /**
     * Event names
     *
     * @var string
     */
    const EVENT_SUCCESS = 'auth.success';
    const EVENT_FAILURE = 'auth.failure';

    /**
     * List of authentication providers
     *
     * @access private
     * @var array
     */
    private $providers = array();

    public function reset()
    {
        $this->providers = [];
    }

    /**
     * Register a new authentication provider
     *
     * @access public
     * @param  AuthenticationProviderInterface $provider
     * @return AuthenticationManager
     */
    public function register(AuthenticationProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
        return $this;
    }

    /**
     * Register a new authentication provider
     *
     * @access public
     * @param  string $name
     * @return AuthenticationProviderInterface|OAuthAuthenticationProviderInterface|PasswordAuthenticationProviderInterface|PreAuthenticationProviderInterface|OAuthAuthenticationProviderInterface
     */
    public function getProvider($name)
    {
        if (! isset($this->providers[$name])) {
            throw new LogicException('Authentication provider not found: '.$name);
        }

        return $this->providers[$name];
    }

    /**
     * Execute providers that are able to validate the current session
     *
     * @access public
     * @return boolean
     */
    public function checkCurrentSession()
    {
        if ($this->userSession->isLogged()) {
            foreach ($this->filterProviders('SessionCheckProviderInterface') as $provider) {
                if (! $provider->isValidSession()) {
                    $this->logger->debug('Invalidate session for '.$this->userSession->getUsername());
                    session_flush();
                    $this->preAuthentication();
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Execute pre-authentication providers
     *
     * @access public
     * @return boolean
     */
    public function preAuthentication()
    {
        foreach ($this->filterProviders('PreAuthenticationProviderInterface') as $provider) {
            if ($provider->authenticate() && $this->userProfile->initialize($provider->getUser())) {
                $this->dispatcher->dispatch(new AuthSuccessEvent($provider->getName()), self::EVENT_SUCCESS);
                return true;
            }
        }

        return false;
    }

    /**
     * Execute username/password authentication providers
     *
     * @access public
     * @param  string  $username
     * @param  string  $password
     * @param  boolean $fireEvent
     * @return boolean
     */
    public function passwordAuthentication($username, $password, $fireEvent = true)
    {
        foreach ($this->filterProviders('PasswordAuthenticationProviderInterface') as $provider) {
            $provider->setUsername($username);
            $provider->setPassword($password);

            if ($provider->authenticate() && $this->userProfile->initialize($provider->getUser())) {
                if ($fireEvent) {
                    $this->dispatcher->dispatch(new AuthSuccessEvent($provider->getName()), self::EVENT_SUCCESS);
                }

                return true;
            }
        }

        if ($fireEvent) {
            $this->dispatcher->dispatch(new AuthFailureEvent($username), self::EVENT_FAILURE);
        }

        return false;
    }

    /**
     * Perform OAuth2 authentication
     *
     * @access public
     * @param  string  $name
     * @return boolean
     */
    public function oauthAuthentication($name)
    {
        $provider = $this->getProvider($name);

        if ($provider->authenticate() && $this->userProfile->initialize($provider->getUser())) {
            $this->dispatcher->dispatch(new AuthSuccessEvent($provider->getName()), self::EVENT_SUCCESS);
            return true;
        }

        $this->dispatcher->dispatch(new AuthFailureEvent, self::EVENT_FAILURE);

        return false;
    }

    /**
     * Get the last Post-Authentication provider
     *
     * @access public
     * @return PostAuthenticationProviderInterface
     */
    public function getPostAuthenticationProvider()
    {
        $providers = $this->filterProviders('PostAuthenticationProviderInterface');

        if (empty($providers)) {
            throw new LogicException('You must have at least one Post-Authentication Provider configured');
        }

        return array_pop($providers);
    }

    /**
     * Filter registered providers by interface type
     *
     * @access private
     * @param  string $interface
     * @return array
     */
    private function filterProviders($interface)
    {
        $interface = '\Kanboard\Core\Security\\'.$interface;

        return array_filter($this->providers, function(AuthenticationProviderInterface $provider) use ($interface) {
            return is_a($provider, $interface);
        });
    }
}
