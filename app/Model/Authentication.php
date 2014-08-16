<?php

namespace Model;

use Auth\Database;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

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
        if (! isset($this->registry->$name)) {
            $class = '\Auth\\'.ucfirst($name);
            $this->registry->$name = new $class($this->registry);
        }

        return $this->registry->shared($name);
    }

    /**
     * Check if the current user is authenticated
     *
     * @access public
     * @param  string    $controller    Controller
     * @param  string    $action        Action name
     * @return bool
     */
    public function isAuthenticated($controller, $action)
    {
        // If the action is public we don't need to do any checks
        if ($this->acl->isPublicAction($controller, $action)) {
            return true;
        }

        // If the user is already logged it's ok
        if ($this->acl->isLogged()) {

            // We update each time the RememberMe cookie tokens
            if ($this->backend('rememberMe')->hasCookie()) {
                $this->backend('rememberMe')->refresh();
            }

            return true;
        }

        // We try first with the RememberMe cookie
        if ($this->backend('rememberMe')->authenticate()) {
            return true;
        }

        // Then with the ReverseProxy authentication
        if (REVERSE_PROXY_AUTH && $this->backend('reverseProxy')->authenticate()) {
            return true;
        }

        return false;
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
        $v = new Validator($values, array(
            new Validators\Required('username', t('The username is required')),
            new Validators\MaxLength('username', t('The maximum length is %d characters', 50), 50),
            new Validators\Required('password', t('The password is required')),
        ));

        $result = $v->execute();
        $errors = $v->getErrors();

        if ($result) {

            $authenticated = false;

            // Try first the database auth and then LDAP if activated
            if ($this->backend('database')->authenticate($values['username'], $values['password'])) {
                $authenticated = true;
            }
            else if (LDAP_AUTH && $this->backend('ldap')->authenticate($values['username'], $values['password'])) {
                $authenticated = true;
            }

            if ($authenticated) {

                // Setup the remember me feature
                if (! empty($values['remember_me'])) {

                    $credentials = $this->backend('rememberMe')
                                        ->create($this->acl->getUserId(), $this->user->getIpAddress(), $this->user->getUserAgent());

                    $this->backend('rememberMe')->writeCookie($credentials['token'], $credentials['sequence'], $credentials['expiration']);
                }
            }
            else {
                $result = false;
                $errors['login'] = t('Bad username or password');
            }
        }

        return array(
            $result,
            $errors
        );
    }
}
