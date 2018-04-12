<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Task Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class TaskValidator extends BaseValidator
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
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Integer('column_id', t('This value must be an integer')),
            new Validators\Integer('owner_id', t('This value must be an integer')),
            new Validators\Integer('creator_id', t('This value must be an integer')),
            new Validators\Integer('score', t('This value must be an integer')),
            new Validators\Range('score', t('This value must be in the range %d to %d', -2147483647, 2147483647), -2147483647, 2147483647),
            new Validators\Integer('category_id', t('This value must be an integer')),
            new Validators\Integer('swimlane_id', t('This value must be an integer')),
            new Validators\GreaterThan('swimlane_id', t('This value must be greater than %d', 0), 0),
            new Validators\Integer('recurrence_child', t('This value must be an integer')),
            new Validators\Integer('recurrence_parent', t('This value must be an integer')),
            new Validators\Integer('recurrence_factor', t('This value must be an integer')),
            new Validators\Integer('recurrence_timeframe', t('This value must be an integer')),
            new Validators\Integer('recurrence_basedate', t('This value must be an integer')),
            new Validators\Integer('recurrence_trigger', t('This value must be an integer')),
            new Validators\Integer('recurrence_status', t('This value must be an integer')),
            new Validators\Integer('priority', t('This value must be an integer')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 65535), 65535),
            new Validators\MaxLength('reference', t('The maximum length is %d characters', 191), 191),
            new Validators\Date('date_due', t('Invalid date'), $this->dateParser->getParserFormats()),
            new Validators\Date('date_started', t('Invalid date'), $this->dateParser->getParserFormats()),
            new Validators\Numeric('time_spent', t('This value must be numeric')),
            new Validators\Numeric('time_estimated', t('This value must be numeric')),
        );
    }

    public function validateStartAndDueDate(array $values)
    {
        if (!empty($values['date_started']) && !empty($values['date_due'])) {
            $startDate = $this->dateParser->getTimestamp($values['date_started']);
            $endDate = $this->dateParser->getTimestamp($values['date_due']);

            if ($startDate > $endDate) {
                return array(false, array('date_started' => array(t('The start date is greater than the end date'))));
            }
        }

        return array(true, array());
    }

    /**
     * Validate task creation
     *
     * @access public
     * @param  array    $values           Form values
     * @return array    $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $rules = array(
            new Validators\Required('project_id', t('The project is required')),
            new Validators\Required('title', t('The title is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));
        $result = $v->execute();
        $errors = $v->getErrors();

        if ($result) {
            list($result, $errors) = $this->validateStartAndDueDate($values);
        }

        return array($result, $errors);
    }

    /**
     * Validate task creation
     *
     * @access public
     * @param  array    $values           Form values
     * @return array    $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateBulkCreation(array $values)
    {
        $rules = array(
            new Validators\Required('tasks', t('Field required')),
            new Validators\Required('column_id', t('Field required')),
            new Validators\Required('swimlane_id', t('Field required')),
            new Validators\Integer('category_id', t('This value must be an integer')),
            new Validators\Integer('swimlane_id', t('This value must be an integer')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate edit recurrence
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateEditRecurrence(array $values)
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
     * Validate task modification (form)
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Required('title', t('The title is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));
        $result = $v->execute();
        $errors = $v->getErrors();

        if ($result) {
            list($result, $errors) = $this->validateStartAndDueDate($values);
        }

        return array($result, $errors);
    }

    /**
     * Validate task modification (Api)
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateApiModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));
        $result = $v->execute();
        $errors = $v->getErrors();

        if ($result) {
            list($result, $errors) = $this->validateStartAndDueDate($values);
        }

        return array($result, $errors);
    }

    /**
     * Validate project modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateProjectModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The id is required')),
            new Validators\Required('project_id', t('The project is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate task email creation
     *
     * @access public
     * @param  array   $values           Required parameters to save an action
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateEmailCreation(array $values)
    {
        $rules = array(
            new Validators\Required('subject', t('This field is required')),
            new Validators\Required('emails', t('This field is required')),
        );

        $v = new Validator($values, $rules);

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
