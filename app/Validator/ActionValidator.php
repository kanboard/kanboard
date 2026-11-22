<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Action Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class ActionValidator extends BaseValidator
{
    /**
     * Validate action creation
     *
     * @access public
     * @param  array   $values           Required parameters to save an action
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateCreation(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('project_id', t('The project id is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('event_name', t('This value is required')),
            new Validators\Required('action_name', t('This value is required')),
            new Validators\Required('params', t('This value is required')),
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate action parameters
     *
     * Parameters are submitted as a free-form array and the form fields are the
     * only place where the choices are restricted. Automatic actions are also
     * executed from an event listener where no ACL is applied, so every
     * reference must be constrained here: resources have to belong to the
     * project that owns the action and the user creating the action must be
     * allowed to create tasks in the destination projects.
     *
     * @access public
     * @param  integer $project_id  Project that owns the action
     * @param  integer $user_id     User that creates the action
     * @param  array   $params      Action parameters
     * @return boolean
     */
    public function validateParameters($project_id, $user_id, array $params)
    {
        foreach ($params as $name => $value) {
            if (! is_scalar($value) || ! $this->isValidParameter($project_id, $name, $value)) {
                return false;
            }
        }

        return $this->validateDestinationProjects($user_id, $params);
    }

    /**
     * Validate the destination projects of action parameters
     *
     * Also used when actions are copied from another project, where the
     * other parameters are resolved against the new project.
     *
     * @access public
     * @param  integer $user_id     User that creates the action
     * @param  array   $params      Action parameters
     * @return boolean
     */
    public function validateDestinationProjects($user_id, array $params)
    {
        foreach ($params as $name => $value) {
            if (strpos($name, 'project_id') !== false && ! $this->canCreateTaskInDestination((int) $value, $user_id, $params)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate one action parameter
     *
     * Parameter names are matched the same way as the form template
     * (action_creation/params.php) to handle the variations used by the
     * actions: src_column_id, dest_swimlane_id, dst_project_id, etc.
     *
     * @access private
     * @param  integer $project_id
     * @param  string  $name
     * @param  mixed   $value
     * @return boolean
     */
    private function isValidParameter($project_id, $name, $value)
    {
        if (strpos($name, 'project_id') !== false) {
            return $this->isValidId($value);
        }

        $allowed_values = $this->getProjectValues($project_id, $name);

        if ($allowed_values === null) {
            return true;
        }

        return $this->isValidId($value) && isset($allowed_values[$value]);
    }

    /**
     * Check that the value is usable as a resource id
     *
     * Anything else (float, boolean, string with leading zero) is rejected
     * because it would not match the identifiers of the project resources.
     *
     * @access private
     * @param  mixed $value
     * @return boolean
     */
    private function isValidId($value)
    {
        return is_int($value) || (is_string($value) && ctype_digit($value));
    }

    /**
     * Get the values allowed for a parameter scoped to the action's project
     *
     * Returns null when the parameter is not a reference to a project resource.
     *
     * @access private
     * @param  integer $project_id
     * @param  string  $name
     * @return array|null
     */
    private function getProjectValues($project_id, $name)
    {
        if (strpos($name, 'column_id') !== false) {
            return $this->columnModel->getList($project_id);
        } elseif (strpos($name, 'swimlane_id') !== false) {
            return $this->swimlaneModel->getList($project_id);
        } elseif (strpos($name, 'category_id') !== false) {
            return $this->categoryModel->getList($project_id);
        } elseif (strpos($name, 'user_id') !== false || strpos($name, 'owner_id') !== false) {
            return $this->projectUserRoleModel->getAssignableUsersList($project_id);
        }

        return null;
    }

    /**
     * Check that the user can create a task in the destination project
     *
     * Tasks land in the first column (TaskDuplicateAnotherProject) or in the
     * column matching the title of the trigger column (TaskMoveAnotherProject),
     * so both columns are checked. The task creation checks apply to the
     * logged user, which is the user creating the action.
     *
     * @access private
     * @param  integer $project_id  Destination project id
     * @param  integer $user_id     User that creates the action
     * @param  array   $params      Action parameters
     * @return boolean
     */
    private function canCreateTaskInDestination($project_id, $user_id, array $params)
    {
        // There is no user session when the API is used with the application credentials
        if (! $this->userSession->isLogged()) {
            return true;
        }

        if (! $this->projectPermissionModel->isUserAllowed($project_id, $user_id)) {
            return false;
        }

        $column_ids = array($this->columnModel->getFirstColumnId($project_id));

        if (isset($params['column_id'])) {
            $column_ids[] = $this->taskDuplicationModel->getDestinationColumnId($project_id, $params['column_id']);
        }

        foreach (array_unique($column_ids) as $column_id) {
            if (! $this->helper->projectRole->canCreateTaskInColumn($project_id, $column_id)) {
                return false;
            }
        }

        return true;
    }
}
