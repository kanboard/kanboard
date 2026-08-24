<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Subtask Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class SubtaskValidator extends BaseValidator
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
        $rules = array(
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('title', t('The title is required')),
        );

        return $this->executeRules($values, array_merge($rules, $this->commonValidationRules()));
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
            new Validators\Required('id', t('The subtask id is required')),
            new Validators\Required('task_id', t('The task id is required')),
            new Validators\Required('title', t('The title is required')),
        );

        return $this->executeRules($values, array_merge($rules, $this->commonValidationRules()));
    }

    /**
     * Validate API modification
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateApiModification(array $values)
    {
        $rules = array(
            new Validators\Required('id', t('The subtask id is required')),
            new Validators\Required('task_id', t('The task id is required')),
        );

        return $this->executeRules($values, array_merge($rules, $this->commonValidationRules()));
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
            new Validators\Integer('id', t('The subtask id must be an integer')),
            new Validators\Integer('task_id', t('The task id must be an integer')),
            new Validators\MaxLength('title', t('The maximum length is %d characters', 65535), 65535),
            new Validators\Integer('user_id', t('The user id must be an integer')),
            new Validators\Integer('status', t('The status must be an integer')),
            new Validators\Numeric('time_estimated', t('The time must be a numeric value')),
            new Validators\Numeric('time_spent', t('The time must be a numeric value')),
        );
    }

    /**
     * Execute the validation rules and check the assignee
     *
     * @access private
     * @param  array   $values           Form values
     * @param  array   $rules            List of validation rules
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    private function executeRules(array $values, array $rules)
    {
        $v = new Validator($values, $rules);
        $result = $v->execute();

        // The values are checked only when they have the expected type
        if ($result && ! $this->isValidAssignee($values)) {
            $v->addError('user_id', t('This user is not allowed to be assigned to this task'));
            $result = false;
        }

        return array($result, $v->getErrors());
    }

    /**
     * Check that the assignee is a member of the project that owns the task
     *
     * The assignee cannot be checked with a validation rule because the list of
     * allowed users depends on the task. The form <select> is the only place
     * where the choices are restricted, and the API passes the value straight
     * to the model, so a subtask could be assigned to somebody outside of the
     * project. Those subtasks show up in the dashboard of the assignee with the
     * content of a task they are not allowed to read.
     *
     * @access private
     * @param  array $values Form values
     * @return boolean
     */
    private function isValidAssignee(array $values)
    {
        if (empty($values['user_id'])) {
            return true;
        }

        if (empty($values['task_id'])) {
            return false;
        }

        $project_id = $this->taskFinderModel->getProjectId($values['task_id']);

        return $project_id > 0 && $this->projectPermissionModel->isAssignable($project_id, $values['user_id']);
    }
}
