<?php

namespace Model;

require_once __DIR__.'/base.php';
require_once __DIR__.'/task.php';

use \SimpleValidator\Validator;
use \SimpleValidator\Validators;

/**
 * Action model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Action extends Base
{
    /**
     * SQL table name for actions
     *
     * @var string
     */
    const TABLE = 'actions';

    /**
     * SQL table name for action parameters
     *
     * @var string
     */
    const TABLE_PARAMS = 'action_has_params';

    /**
     * Return the name and description of available actions
     *
     * @access public
     * @return array
     */
    public function getAvailableActions()
    {
        return array(
            'TaskClose' => t('Close the task'),
            'TaskAssignSpecificUser' => t('Assign the task to a specific user'),
            'TaskAssignCurrentUser' => t('Assign the task to the person who does the action'),
            'TaskDuplicateAnotherProject' => t('Duplicate the task to another project'),
            'TaskAssignColorUser' => t('Assign a color to a specific user'),
        );
    }

    /**
     * Return the name and description of available actions
     *
     * @access public
     * @return array
     */
    public function getAvailableEvents()
    {
        return array(
            Task::EVENT_MOVE_COLUMN => t('Move a task to another column'),
            Task::EVENT_MOVE_POSITION => t('Move a task to another position in the same column'),
            Task::EVENT_UPDATE => t('Task modification'),
            Task::EVENT_CREATE => t('Task creation'),
            Task::EVENT_OPEN => t('Open a closed task'),
            Task::EVENT_CLOSE => t('Closing a task'),
        );
    }

    /**
     * Return actions and parameters for a given project
     *
     * @access public
     * @return array
     */
    public function getAllByProject($project_id)
    {
        $actions = $this->db->table(self::TABLE)->eq('project_id', $project_id)->findAll();

        foreach ($actions as &$action) {
            $action['params'] = $this->db->table(self::TABLE_PARAMS)->eq('action_id', $action['id'])->findAll();
        }

        return $actions;
    }

    /**
     * Return all actions and parameters
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        $actions = $this->db->table(self::TABLE)->findAll();

        foreach ($actions as &$action) {
            $action['params'] = $this->db->table(self::TABLE_PARAMS)->eq('action_id', $action['id'])->findAll();
        }

        return $actions;
    }

    /**
     * Get all required action parameters for all registered actions
     *
     * @access public
     * @return array  All required parameters for all actions
     */
    public function getAllActionParameters()
    {
        $params = array();

        foreach ($this->getAll() as $action) {

            $action = $this->load($action['action_name'], $action['project_id']);
            $params += $action->getActionRequiredParameters();
        }

        return $params;
    }

    /**
     * Fetch an action
     *
     * @access public
     * @param  integer $action_id  Action id
     * @return array               Action data
     */
    public function getById($action_id)
    {
        $action = $this->db->table(self::TABLE)->eq('id', $action_id)->findOne();
        $action['params'] = $this->db->table(self::TABLE_PARAMS)->eq('action_id', $action_id)->findAll();

        return $action;
    }

    /**
     * Remove an action
     *
     * @access public
     * @param  integer $action_id  Action id
     * @return bool                Success or not
     */
    public function remove($action_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $action_id)->remove();
    }

    /**
     * Create an action
     *
     * @access public
     * @param  array   $values  Required parameters to save an action
     * @return bool             Success or not
     */
    public function create(array $values)
    {
        $this->db->startTransaction();

        $action = array(
            'project_id' => $values['project_id'],
            'event_name' => $values['event_name'],
            'action_name' => $values['action_name'],
        );

        if (! $this->db->table(self::TABLE)->save($action)) {
            $this->db->cancelTransaction();
            return false;
        }

        $action_id = $this->db->getConnection()->getLastId();

        foreach ($values['params'] as $param_name => $param_value) {

            $action_param = array(
                'action_id' => $action_id,
                'name' => $param_name,
                'value' => $param_value,
            );

            if (! $this->db->table(self::TABLE_PARAMS)->save($action_param)) {
                $this->db->cancelTransaction();
                return false;
            }
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Load all actions and attach events
     *
     * @access public
     */
    public function attachEvents()
    {
        foreach ($this->getAll() as $action) {

            $listener = $this->load($action['action_name'], $action['project_id']);

            foreach ($action['params'] as $param) {
                $listener->setParam($param['name'], $param['value']);
            }

            $this->event->attach($action['event_name'], $listener);
        }
    }

    /**
     * Load an action
     *
     * @access public
     * @param  string  $name        Action class name
     * @param  integer $project_id  Project id
     * @return mixed                Action Instance
     * @throw  LogicException
     */
    public function load($name, $project_id)
    {
        switch ($name) {
            case 'TaskClose':
                require_once __DIR__.'/../actions/task_close.php';
                $className = '\Action\TaskClose';
                return new $className($project_id, new Task($this->db, $this->event));
            case 'TaskAssignCurrentUser':
                require_once __DIR__.'/../actions/task_assign_current_user.php';
                $className = '\Action\TaskAssignCurrentUser';
                return new $className($project_id, new Task($this->db, $this->event), new Acl($this->db, $this->event));
            case 'TaskAssignSpecificUser':
                require_once __DIR__.'/../actions/task_assign_specific_user.php';
                $className = '\Action\TaskAssignSpecificUser';
                return new $className($project_id, new Task($this->db, $this->event));
            case 'TaskDuplicateAnotherProject':
                require_once __DIR__.'/../actions/task_duplicate_another_project.php';
                $className = '\Action\TaskDuplicateAnotherProject';
                return new $className($project_id, new Task($this->db, $this->event));
            case 'TaskAssignColorUser':
                require_once __DIR__.'/../actions/task_assign_color_user.php';
                $className = '\Action\TaskAssignColorUser';
                return new $className($project_id, new Task($this->db, $this->event));
            default:
                throw new \LogicException('Action not found: '.$name);
        }
    }

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
}
