<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Kanboard\Model\GroupModel;

/**
 * Group Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class GroupValidator extends BaseValidator
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
            new Validators\Required('name', t('The name is required')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 100), 100),
            new Validators\Unique('name', t('The name must be unique'), $this->db->getConnection(), GroupModel::TABLE, 'id'),
            new Validators\MaxLength('external_id', t('The maximum length is %d characters', 255), 255),
            new Validators\Integer('id', t('This value must be an integer')),
        );
    }
}
