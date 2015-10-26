<?php

namespace Kanboard\Model;

use Kanboard\Integration\GitlabWebhook;
use Kanboard\Integration\GithubWebhook;
use Kanboard\Integration\BitbucketWebhook;
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
     * Extended actions
     *
     * @access private
     * @var array
     */
    private $actions = array();

    /**
     * Extend the list of default actions
     *
     * @access public
     * @param  string  $className
     * @param  string  $description
     * @return Action
     */
    public function extendActions($className, $description)
    {
        $this->actions[$className] = $description;
        return $this;
    }

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
            'TaskMoveColumnAssigned' => t('Move the task to another column when assigned to a user'),
            'TaskMoveColumnUnAssigned' => t('Move the task to another column when assignee is cleared'),
            'TaskAssignColorColumn' => t('Assign a color when the task is moved to a specific column'),
            'TaskAssignColorUser' => t('Assign a color to a specific user'),
            'TaskAssignColorCategory' => t('Assign automatically a color based on a category'),
            'TaskAssignCategoryColor' => t('Assign automatically a category based on a color'),
            'TaskAssignCategoryLink' => t('Assign automatically a category based on a link'),
            'CommentCreation' => t('Create a comment from an external provider'),
            'TaskCreation' => t('Create a task from an external provider'),
            'TaskLogMoveAnotherColumn' => t('Add a comment log when moving the task between columns'),
            'TaskAssignUser' => t('Change the assignee based on an external username'),
            'TaskAssignCategoryLabel' => t('Change the category based on an external label'),
            'TaskUpdateStartDate' => t('Automatically update the start date'),
            'TaskMoveColumnCategoryChange' => t('Move the task to another column when the category is changed'),
            'TaskEmail' => t('Send a task by email to someone'),
            'TaskAssignColorLink' => t('Change task color when using a specific task link'),
        );

        $values = array_merge($values, $this->actions);

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
            TaskLink::EVENT_CREATE_UPDATE => t('Task link creation or modification'),
            Task::EVENT_MOVE_COLUMN => t('Move a task to another column'),
            Task::EVENT_UPDATE => t('Task modification'),
            Task::EVENT_CREATE => t('Task creation'),
            Task::EVENT_OPEN => t('Reopen a task'),
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
            GitlabWebhook::EVENT_ISSUE_COMMENT => t('Gitlab issue comment created'),
            BitbucketWebhook::EVENT_COMMIT => t('Bitbucket commit received'),
            BitbucketWebhook::EVENT_ISSUE_OPENED => t('Bitbucket issue opened'),
            BitbucketWebhook::EVENT_ISSUE_CLOSED => t('Bitbucket issue closed'),
            BitbucketWebhook::EVENT_ISSUE_REOPENED => t('Bitbucket issue reopened'),
            BitbucketWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE => t('Bitbucket issue assignee change'),
            BitbucketWebhook::EVENT_ISSUE_COMMENT => t('Bitbucket issue comment created'),
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

        if (! empty($action)) {
            $action['params'] = $this->db->table(self::TABLE_PARAMS)->eq('action_id', $action_id)->findAll();
        }

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
     * @return boolean|integer
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

        $action_id = $this->db->getLastId();

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

        return $action_id;
    }

    /**
     * Load all actions and attach events
     *
     * @access public
     */
    public function attachEvents()
    {
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
     * @return \Action\Base
     */
    public function load($name, $project_id, $event)
    {
        $className = $name{0}
        !== '\\' ? '\Kanboard\Action\\'.$name : $name;
        return new $className($this->container, $project_id, $event);
    }

    /**
     * Copy actions from a project to another one (skip actions that cannot resolve parameters)
     *
     * @author Antonio Rabelo
     * @param  integer    $src_project_id      Source project id
     * @return integer    $dst_project_id      Destination project id
     * @return boolean
     */
    public function duplicate($src_project_id, $dst_project_id)
    {
        $actions = $this->action->getAllByProject($src_project_id);

        foreach ($actions as $action) {
            $this->db->startTransaction();

            $values = array(
                'project_id' => $dst_project_id,
                'event_name' => $action['event_name'],
                'action_name' => $action['action_name'],
            );

            if (! $this->db->table(self::TABLE)->insert($values)) {
                $this->container['logger']->debug('Action::duplicate => unable to create '.$action['action_name']);
                $this->db->cancelTransaction();
                continue;
            }

            $action_id = $this->db->getLastId();

            if (! $this->duplicateParameters($dst_project_id, $action_id, $action['params'])) {
                $this->container['logger']->debug('Action::duplicate => unable to copy parameters for '.$action['action_name']);
                $this->db->cancelTransaction();
                continue;
            }

            $this->db->closeTransaction();
        }

        return true;
    }

    /**
     * Duplicate action parameters
     *
     * @access public
     * @param  integer  $project_id
     * @param  integer  $action_id
     * @param  array    $params
     * @return boolean
     */
    public function duplicateParameters($project_id, $action_id, array $params)
    {
        foreach ($params as $param) {
            $value = $this->resolveParameters($param, $project_id);

            if ($value === false) {
                $this->container['logger']->debug('Action::duplicateParameters => unable to resolve '.$param['name'].'='.$param['value']);
                return false;
            }

            $values = array(
                'action_id' => $action_id,
                'name' => $param['name'],
                'value' => $value,
            );

            if (! $this->db->table(self::TABLE_PARAMS)->insert($values)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Resolve action parameter values according to another project
     *
     * @author Antonio Rabelo
     * @access public
     * @param  array      $param             Action parameter
     * @param  integer    $project_id        Project to find the corresponding values
     * @return mixed
     */
    public function resolveParameters(array $param, $project_id)
    {
        switch ($param['name']) {
            case 'project_id':
                return $project_id;
            case 'category_id':
                return $this->category->getIdByName($project_id, $this->category->getNameById($param['value'])) ?: false;
            case 'src_column_id':
            case 'dest_column_id':
            case 'dst_column_id':
            case 'column_id':
                $column = $this->board->getColumn($param['value']);

                if (empty($column)) {
                    return false;
                }

                return $this->board->getColumnIdByTitle($project_id, $column['title']) ?: false;
            case 'user_id':
            case 'owner_id':
                return $this->projectPermission->isMember($project_id, $param['value']) ? $param['value'] : false;
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
