<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Gregwar\Captcha\CaptchaBuilder;

/**
 * Password Reset Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class PasswordResetValidator extends BaseValidator
{
    /**
     * Validate creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        return $this->executeValidators(array('validateFields', 'validateCaptcha'), $values);
    }

    /**
     * Validate modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $v = new Validator($values, $this->commonPasswordValidationRules());

        return array(
            $v->execute(),
            $v->getErrors(),
        );
    }

    /**
     * Validate fields
     *
     * @access protected
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    protected function validateFields(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('captcha', t('This value is required')),
            new Validators\Required('username', t('The username is required')),
            new Validators\MaxLength('username', t('The maximum length is %d characters', 191), 191),
        ));

        return array(
            $v->execute(),
            $v->getErrors(),
        );
    }

    /**
     * Validate captcha
     *
     * @access protected
     * @param  array   $values           Form values
     * @return array
     */
    protected function validateCaptcha(array $values)
    {
        $errors = array();

        if (! session_exists('captcha')) {
            $result = false;
        } else {
            $builder = new CaptchaBuilder;
            $builder->setPhrase(session_get('captcha'));
            $result = $builder->testPhrase(isset($values['captcha']) ? $values['captcha'] : '');

            if (! $result) {
                $errors['captcha'] = array(t('Invalid captcha'));
            }

            // Invalidate captcha to avoid reuse.
            session_remove('captcha');
        }

        return array($result, $errors);
    }
}
