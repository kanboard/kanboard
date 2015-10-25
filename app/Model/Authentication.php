<?php

namespace Kanboard\Model;

use Kanboard\Core\Http\Request;
use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Gregwar\Captcha\CaptchaBuilder;

/**
 * Authentication model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Authentication extends Base
{
    /**
     * Load automatically an authentication backend
     *
     * @access public
     * @param  string   $name    Backend class name
     * @return mixed
     */
    public function backend($name)
    {
        if (! isset($this->container[$name])) {
            $class = '\Kanboard\Auth\\'.ucfirst($name);
            $this->container[$name] = new $class($this->container);
        }

        return $this->container[$name];
    }

    /**
     * Check if the current user is authenticated
     *
     * @access public
     * @return bool
     */
    public function isAuthenticated()
    {
        // If the user is already logged it's ok
        if ($this->userSession->isLogged()) {

            // Check if the user session match an existing user
            $userNotFound = ! $this->user->exists($this->userSession->getId());
            $reverseProxyWrongUser = REVERSE_PROXY_AUTH && $this->backend('reverseProxy')->getUsername() !== $_SESSION['user']['username'];

            if ($userNotFound || $reverseProxyWrongUser) {
                $this->backend('rememberMe')->destroy($this->userSession->getId());
                $this->session->close();
                return false;
            }

            return true;
        }

        // We try first with the RememberMe cookie
        if (REMEMBER_ME_AUTH && $this->backend('rememberMe')->authenticate()) {
            return true;
        }

        // Then with the ReverseProxy authentication
        if (REVERSE_PROXY_AUTH && $this->backend('reverseProxy')->authenticate()) {
            return true;
        }

        return false;
    }

    /**
     * Authenticate a user by different methods
     *
     * @access public
     * @param  string  $username  Username
     * @param  string  $password  Password
     * @return boolean
     */
    public function authenticate($username, $password)
    {
        if ($this->user->isLocked($username)) {
            $this->container['logger']->error('Account locked: '.$username);
            return false;
        } elseif ($this->backend('database')->authenticate($username, $password)) {
            $this->user->resetFailedLogin($username);
            return true;
        } elseif (LDAP_AUTH && $this->backend('ldap')->authenticate($username, $password)) {
            $this->user->resetFailedLogin($username);
            return true;
        }

        $this->handleFailedLogin($username);
        return false;
    }

    /**
     * Return true if the captcha must be shown
     *
     * @access public
     * @param  string  $username
     * @return boolean
     */
    public function hasCaptcha($username)
    {
        return $this->user->getFailedLogin($username) >= BRUTEFORCE_CAPTCHA;
    }

    /**
     * Handle failed login
     *
     * @access public
     * @param  string  $username
     */
    public function handleFailedLogin($username)
    {
        $this->user->incrementFailedLogin($username);

        if ($this->user->getFailedLogin($username) >= BRUTEFORCE_LOCKDOWN) {
            $this->container['logger']->critical('Locking account: '.$username);
            $this->user->lock($username, BRUTEFORCE_LOCKDOWN_DURATION);
        }
    }

    /**
     * Validate user login form
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateForm(array $values)
    {
        list($result, $errors) = $this->validateFormCredentials($values);

        if ($result) {
            if ($this->validateFormCaptcha($values) && $this->authenticate($values['username'], $values['password'])) {
                $this->createRememberMeSession($values);
            } else {
                $result = false;
                $errors['login'] = t('Bad username or password');
            }
        }

        return array($result, $errors);
    }

    /**
     * Validate credentials syntax
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateFormCredentials(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('username', t('The username is required')),
            new Validators\MaxLength('username', t('The maximum length is %d characters', 50), 50),
            new Validators\Required('password', t('The password is required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors(),
        );
    }

    /**
     * Validate captcha
     *
     * @access public
     * @param  array   $values           Form values
     * @return boolean
     */
    public function validateFormCaptcha(array $values)
    {
        if ($this->hasCaptcha($values['username'])) {
            $builder = new CaptchaBuilder;
            $builder->setPhrase($this->session['captcha']);
            return $builder->testPhrase(isset($values['captcha']) ? $values['captcha'] : '');
        }

        return true;
    }

    /**
     * Create remember me session if necessary
     *
     * @access private
     * @param  array   $values           Form values
     */
    private function createRememberMeSession(array $values)
    {
        if (REMEMBER_ME_AUTH && ! empty($values['remember_me'])) {
            $credentials = $this->backend('rememberMe')
                                ->create($this->userSession->getId(), Request::getIpAddress(), Request::getUserAgent());

            $this->backend('rememberMe')->writeCookie($credentials['token'], $credentials['sequence'], $credentials['expiration']);
        }
    }
}
