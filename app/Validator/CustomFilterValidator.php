<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Custom Filter Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class CustomFilterValidator extends BaseValidator
{
    /**
     * Common validation rules
     *
     * @access private
     * @return array
     */
    private function commonValidationRules()
    {
        return array(
            new Validators\Required('project_id', t('Field required')),
            new Validators\Required('user_id', t('Field required')),
            new Validators\Required('name', t('Field required')),
            new Validators\Required('filter', t('Field required')),
            new Validators\Integer('user_id', t('This value must be an integer')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 100), 100),
            new Validators\MaxLength('filter', t('The maximum length is %d characters', 100), 100)
        );
    }

    /**
     * Validate filter creation
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
     * Validate filter modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('Field required')),
            new Validators\Integer('id', t('This value must be an integer')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
