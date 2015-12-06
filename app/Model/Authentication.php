<?php

namespace Kanboard\Model;

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
     * Validate user login form
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateForm(array $values)
    {
        $result = false;
        $errors = array();

        foreach (array('validateFields', 'validateLocking', 'validateCaptcha', 'validateCredentials') as $method) {
            list($result, $errors) = $this->$method($values);

            if (! $result) {
                break;
            }
        }

        return array($result, $errors);
    }

    /**
     * Validate credentials syntax
     *
     * @access private
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    private function validateFields(array $values)
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
     * Validate user locking
     *
     * @access private
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    private function validateLocking(array $values)
    {
        $result = true;
        $errors = array();

        if ($this->userLocking->isLocked($values['username'])) {
            $result = false;
            $errors['login'] = t('Your account is locked for %d minutes', BRUTEFORCE_LOCKDOWN_DURATION);
            $this->logger->error('Account locked: '.$values['username']);
        }

        return array($result, $errors);
    }

    /**
     * Validate password syntax
     *
     * @access private
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    private function validateCredentials(array $values)
    {
        $result = true;
        $errors = array();

        if (! $this->authenticationManager->passwordAuthentication($values['username'], $values['password'])) {
            $result = false;
            $errors['login'] = t('Bad username or password');
        }

        return array($result, $errors);
    }

    /**
     * Validate captcha
     *
     * @access private
     * @param  array   $values           Form values
     * @return boolean
     */
    private function validateCaptcha(array $values)
    {
        $result = true;
        $errors = array();

        if ($this->userLocking->hasCaptcha($values['username'])) {
            if (! isset($this->sessionStorage->captcha)) {
                $result = false;
            } else {
                $builder = new CaptchaBuilder;
                $builder->setPhrase($this->sessionStorage->captcha);
                $result = $builder->testPhrase(isset($values['captcha']) ? $values['captcha'] : '');

                if (! $result) {
                    $errors['login'] = t('Invalid captcha');
                }
            }
        }

        return array($result, $errors);;
    }
}
