<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * External Link Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class ExternalLinkValidator extends BaseValidator
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
        $v = new Validator($values, $this->commonValidationRules());

        return array(
            $v->execute(),
            $v->getErrors()
        );
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
        $rules = array(
            new Validators\Required('id', t('The id is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        return array(
            new Validators\Required('url', t('Field required')),
            new Validators\MaxLength('url', t('The maximum length is %d characters', 65535), 65535),
            new Validators\URL('url', t('This URL is invalid')),
            new Validators\Required('title', t('Field required')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 65535), 65535),
            new Validators\Required('link_type', t('Field required')),
            new Validators\MaxLength('link_type', t('The maximum length is %d characters', 100), 100),
            new Validators\Required('dependency', t('Field required')),
            new Validators\MaxLength('dependency', t('The maximum length is %d characters', 100), 100),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('task_id', t('Field required')),
            new Validators\Integer('task_id', t('This value must be an integer')),
        );
    }
}
