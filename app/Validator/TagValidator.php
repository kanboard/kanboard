<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Tag Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class TagValidator extends BaseValidator
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
        $result = $v->execute();
        $errors = $v->getErrors();

        if ($result && $this->tagModel->exists($values['project_id'], $values['name'])) {
            $result = false;
            $errors = array('name' => array(t('The name must be unique')));
        }

        return array($result, $errors);
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
            new Validators\Required('id', t('Field required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));
        $result = $v->execute();
        $errors = $v->getErrors();

        if ($result && $this->tagModel->exists($values['project_id'], $values['name'], $values['id'])) {
            $result = false;
            $errors = array('name' => array(t('The name must be unique')));
        }

        return array($result, $errors);
    }

    /**
     * Common validation rules
     *
     * @access protected
     * @return array
     */
    protected function commonValidationRules()
    {
        return array(
            new Validators\Required('project_id', t('Field required')),
            new Validators\Required('name', t('Field required')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 191), 191),
        );
    }
}
