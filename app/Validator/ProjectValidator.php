<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Model\ProjectModel;

/**
 * Project Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class ProjectValidator extends BaseValidator
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
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Integer('priority_default', t('This value must be an integer')),
            new Validators\Integer('priority_start', t('This value must be an integer')),
            new Validators\Integer('priority_end', t('This value must be an integer')),
            new Validators\Integer('is_active', t('This value must be an integer')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 65535), 65535),
            new Validators\MaxLength('identifier', t('The maximum length is %d characters', 50), 50),
            new Validators\MaxLength('start_date', t('The maximum length is %d characters', 10), 10),
            new Validators\MaxLength('end_date', t('The maximum length is %d characters', 10), 10),
            new Validators\AlphaNumeric('identifier', t('This value must be alphanumeric')) ,
            new Validators\Unique('identifier', t('The identifier must be unique'), $this->db->getConnection(), ProjectModel::TABLE),
            new Validators\Email('email', t('Email address invalid')) ,
            new Validators\Unique('email', t('The project email must be unique across all projects'), $this->db->getConnection(), ProjectModel::TABLE),
        );
    }

    /**
     * Validate project creation
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        if (! empty($values['identifier'])) {
            $values['identifier'] = strtoupper($values['identifier']);
        }

        $rules = array(
            new Validators\Required('name', t('The project name is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate project modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        if (! empty($values['identifier'])) {
            $values['identifier'] = strtoupper($values['identifier']);
        }

        $rules = array(
            new Validators\NotEmpty('name', t('This field cannot be empty')),
            new Validators\Required('id', t('This value is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
