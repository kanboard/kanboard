<?php

namespace Model;

use Integration\GitlabWebhook;
use Integration\GithubWebhook;
use SimpleValidator\Validator;
use SimpleValidator\Validators;

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
        $values = array(
            'TaskClose' => t('Close a task'),
            'TaskOpen' => t('Open a task'),
            'TaskAssignSpecificUser' => t('Assign the task to a specific user'),
            'TaskAssignCurrentUser' => t('Assign the task to the person who does the action'),
            'TaskDuplicateAnotherProject' => t('Duplicate the task to another project'),
            'TaskMoveAnotherProject' => t('Move the task to another project'),
            'TaskAssignColorUser' => t('Assign a color to a specific user'),
            'TaskAssignColorCategory' => t('Assign automatically a color based on a category'),
            'TaskAssignCategoryColor' => t('Assign automatically a category based on a color'),
            'CommentCreation' => t('Create a comment from an external provider'),
            'TaskCreation' => t('Create a task from an external provider'),
            'TaskAssignUser' => t('Change the assignee based on an external username'),
            'TaskAssignCategoryLabel' => t('Change the category based on an external label'),
        );

        asort($values);

        return $values;
    }

    /**
     * Return the name and description of available actions
     *
     * @access public
     * @return array
     */
    public function getAvailableEvents()
    {
        $values = array(
            Task::EVENT_MOVE_COLUMN => t('Move a task to another column'),
            Task::EVENT_UPDATE => t('Task modification'),
            Task::EVENT_CREATE => t('Task creation'),
            Task::EVENT_OPEN => t('Open a closed task'),
            Task::EVENT_CLOSE => t('Closing a task'),
            Task::EVENT_CREATE_UPDATE => t('Task creation or modification'),
            Task::EVENT_ASSIGNEE_CHANGE => t('Task assignee change'),
            GithubWebhook::EVENT_COMMIT => t('Github commit received'),
            GithubWebhook::EVENT_ISSUE_OPENED => t('Github issue opened'),
            GithubWebhook::EVENT_ISSUE_CLOSED => t('Github issue closed'),
            GithubWebhook::EVENT_ISSUE_REOPENED => t('Github issue reopened'),
            GithubWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE => t('Github issue assignee change'),
            GithubWebhook::EVENT_ISSUE_LABEL_CHANGE => t('Github issue label change'),
            GithubWebhook::EVENT_ISSUE_COMMENT => t('Github issue comment created'),
            GitlabWebhook::EVENT_COMMIT => t('Gitlab commit received'),
            GitlabWebhook::EVENT_ISSUE_OPENED => t('Gitlab issue opened'),
            GitlabWebhook::EVENT_ISSUE_CLOSED => t('Gitlab issue closed'),
        );

        asort($values);

        return $values;
    }

    /**
     * Return the name and description of compatible actions
     *
     * @access public
     * @param  string    $action_name   Action name
     * @return array
     */
    public function getCompatibleEvents($action_name)
    {
        $action = $this->load($action_name, 0, '');
        $compatible_events = $action->getCompatibleEvents();
        $events = array();

        foreach ($this->getAvailableEvents() as $event_name => $event_description) {
            if (in_array($event_name, $compatible_events)) {
                $events[$event_name] = $event_description;
            }
        }

        return $events;
    }

    /**
     * Return actions and parameters for a given project
     *
     * @access public
     * @param $project_id
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
        $params = $this->db->table(self::TABLE_PARAMS)->findAll();

        foreach ($actions as &$action) {

            $action['params'] = array();

            foreach ($params as $param) {
                if ($param['action_id'] === $action['id']) {
                    $action['params'][] = $param;
                }
            }
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

            $action = $this->load($action['action_name'], $action['project_id'], $action['event_name']);
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
        // $this->container['fileCache']->remove('proxy_action_getAll');
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

        // $this->container['fileCache']->remove('proxy_action_getAll');

        return true;
    }

    /**
     * Load all actions and attach events
     *
     * @access public
     */
    public function attachEvents()
    {
        //$actions = $this->container['fileCache']->proxy('action', 'getAll');
        $actions = $this->getAll();

        foreach ($actions as $action) {

            $listener = $this->load($action['action_name'], $action['project_id'], $action['event_name']);

            foreach ($action['params'] as $param) {
                $listener->setParam($param['name'], $param['value']);
            }

            $this->container['dispatcher']->addListener($action['event_name'], array($listener, 'execute'));
        }
    }

    /**
     * Load an action
     *
     * @access public
     * @param  string           $name         Action class name
     * @param  integer          $project_id   Project id
     * @param  string           $event        Event name
     * @return \Core\Listener                 Action instance
     */
    public function load($name, $project_id, $event)
    {
        $className = '\Action\\'.$name;
        return new $className($this->container, $project_id, $event);
    }

    /**
     * Copy Actions and related Actions Parameters from a project to another one
     *
     * @author Antonio Rabelo
     * @param  integer    $project_from      Project Template
     * @return integer    $project_to        Project that receives the copy
     * @return boolean
     */
    public function duplicate($project_from, $project_to)
    {
        $actionTemplate = $this->action->getAllByProject($project_from);

        foreach ($actionTemplate as $action) {

            unset($action['id']);
            $action['project_id'] = $project_to;
            $actionParams = $action['params'];
            unset($action['params']);

            if (! $this->db->table(self::TABLE)->save($action)) {
                return false;
            }

            $action_clone_id = $this->db->getConnection()->getLastId();

            foreach ($actionParams as $param) {
                unset($param['id']);
                $param['value'] = $this->resolveDuplicatedParameters($param, $project_to);
                $param['action_id'] = $action_clone_id;

                if (! $this->db->table(self::TABLE_PARAMS)->save($param)) {
                    return false;
                }
            }
        }

        // $this->container['fileCache']->remove('proxy_action_getAll');

        return true;
    }

    /**
     * Resolve type of action value from a project to the respective value in another project
     *
     * @author Antonio Rabelo
     * @param  integer    $param             An action parameter
     * @return integer    $project_to        Project to find the corresponding values
     * @return mixed                         The corresponding values from $project_to
     */
    private function resolveDuplicatedParameters($param, $project_to)
    {
        switch($param['name']) {
            case 'project_id':
                return $project_to;
            case 'category_id':
                $categoryTemplate = $this->category->getById($param['value']);
                $categoryFromNewProject = $this->db->table(Category::TABLE)->eq('project_id', $project_to)->eq('name', $categoryTemplate['name'])->findOne();
                return $categoryFromNewProject['id'];
            case 'column_id':
                $boardTemplate = $this->board->getColumn($param['value']);
                $boardFromNewProject = $this->db->table(Board::TABLE)->eq('project_id', $project_to)->eq('title', $boardTemplate['title'])->findOne();
                return $boardFromNewProject['id'];
            default:
                return $param['value'];
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
