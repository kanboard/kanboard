<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Class ProjectRoleValidator
 *
 * @package Kanboard\Validator
 * @author  Frederic Guillot
 */
class ProjectRoleValidator extends BaseValidator
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
            new Validators\Required('role_id', t('The id is required')),
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
            new Validators\Required('role', t('This field is required')),
            new Validators\MaxLength('role', t('The maximum length is %d characters', 100), 100),
            new Validators\Required('project_id', t('This field is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Integer('role_id', t('This value must be an integer')),
        );
    }
}
