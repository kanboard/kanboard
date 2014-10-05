<?php

namespace Model;

use SimpleValidator\Validator;
use SimpleValidator\Validators;
use Event\ProjectModificationDate;
use Core\Security;

/**
 * Project model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Project extends Base
{
    /**
     * SQL table name for projects
     *
     * @var string
     */
    const TABLE = 'projects';

    /**
     * Value for active project
     *
     * @var integer
     */
    const ACTIVE = 1;

    /**
     * Value for inactive project
     *
     * @var integer
     */
    const INACTIVE = 0;

    /**
     * Get a project by the id
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getById($project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $project_id)->findOne();
    }

    /**
     * Get a project by the name
     *
     * @access public
     * @param  string   $project_name    Project name
     * @return array
     */
    public function getByName($project_name)
    {
        return $this->db->table(self::TABLE)->eq('name', $project_name)->findOne();
    }

    /**
     * Fetch project data by using the token
     *
     * @access public
     * @param  string   $token    Token
     * @return array
     */
    public function getByToken($token)
    {
        return $this->db->table(self::TABLE)->eq('token', $token)->eq('is_public', 1)->findOne();
    }

    /**
     * Return the first project from the database (no sorting)
     *
     * @access public
     * @return array
     */
    public function getFirst()
    {
        return $this->db->table(self::TABLE)->findOne();
    }

    /**
     * Return true if the project is private
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return boolean
     */
    public function isPrivate($project_id)
    {
        return (bool) $this->db->table(self::TABLE)->eq('id', $project_id)->eq('is_private', 1)->count();
    }

    /**
     * Get all projects, optionaly fetch stats for each project and can check users permissions
     *
     * @access public
     * @param  bool       $filter_permissions    If true, remove projects not allowed for the current user
     * @return array
     */
    public function getAll($filter_permissions = false)
    {
        $projects = $this->db->table(self::TABLE)->asc('name')->findAll();

        if ($filter_permissions) {

            foreach ($projects as $key => $project) {

                if (! $this->projectPermission->isUserAllowed($project['id'], $this->acl->getUserId())) {
                    unset($projects[$key]);
                }
            }
        }

        return $projects;
    }

    /**
     * Return the list of all projects
     *
     * @access public
     * @param  bool     $prepend   If true, prepend to the list the value 'None'
     * @return array
     */
    public function getList($prepend = true)
    {
        if ($prepend) {
            return array(t('None')) + $this->db->table(self::TABLE)->asc('name')->listing('id', 'name');
        }

        return $this->db->table(self::TABLE)->asc('name')->listing('id', 'name');
    }

    /**
     * Get all projects with all its data for a given status
     *
     * @access public
     * @param  integer   $status   Proejct status: self::ACTIVE or self:INACTIVE
     * @return array
     */
    public function getAllByStatus($status)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->asc('name')
                    ->eq('is_active', $status)
                    ->findAll();
    }

    /**
     * Get a list of project by status
     *
     * @access public
     * @param  integer   $status   Project status: self::ACTIVE or self:INACTIVE
     * @return array
     */
    public function getListByStatus($status)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->asc('name')
                    ->eq('is_active', $status)
                    ->listing('id', 'name');
    }

    /**
     * Return the number of projects by status
     *
     * @access public
     * @param  integer   $status   Status: self::ACTIVE or self:INACTIVE
     * @return integer
     */
    public function countByStatus($status)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('is_active', $status)
                    ->count();
    }

    /**
     * Gather some task metrics for a given project
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @return array
     */
    public function getStats($project_id)
    {
        $stats = array();
        $columns = $this->board->getcolumns($project_id);
        $stats['nb_active_tasks'] = 0;

        foreach ($columns as &$column) {
            $column['nb_active_tasks'] = $this->task->countByColumnId($project_id, $column['id']);
            $stats['nb_active_tasks'] += $column['nb_active_tasks'];
        }

        $stats['columns'] = $columns;
        $stats['nb_tasks'] = $this->task->countByProjectId($project_id);
        $stats['nb_inactive_tasks'] = $stats['nb_tasks'] - $stats['nb_active_tasks'];

        return $stats;
    }

    /**
     * Create a project from another one.
     *
     * @author Antonio Rabelo
     * @param  integer    $project_id      Project Id
     * @return integer                     Cloned Project Id
     */
    public function createProjectFromAnotherProject($project_id)
    {
        $project = $this->getById($project_id);

        $values = array(
            'name' => $project['name'].' ('.t('Clone').')',
            'is_active' => true,
            'last_modified' => 0,
            'token' => '',
            'is_public' => 0,
            'is_private' => empty($project['is_private']) ? 0 : 1,
        );

        if (! $this->db->table(self::TABLE)->save($values)) {
            return false;
        }

        return $this->db->getConnection()->getLastId();
    }

    /**
     * Clone a project
     *
     * @author Antonio Rabelo
     * @param  integer    $project_id  Project Id
     * @return integer                 Cloned Project Id
     */
    public function duplicate($project_id)
    {
        $this->db->startTransaction();

        // Get the cloned project Id
        $clone_project_id = $this->createProjectFromAnotherProject($project_id);

        if (! $clone_project_id) {
            $this->db->cancelTransaction();
            return false;
        }

        foreach (array('board', 'category', 'projectPermission', 'action') as $model) {

            if (! $this->$model->duplicate($project_id, $clone_project_id)) {
                $this->db->cancelTransaction();
                return false;
            }
        }

        $this->db->closeTransaction();

        return (int) $clone_project_id;
    }

    /**
     * Create a project
     *
     * @access public
     * @param  array    $values   Form values
     * @param  integer  $user_id  User who create the project
     * @return integer            Project id
     */
    public function create(array $values, $user_id = 0)
    {
        $this->db->startTransaction();

        $values['token'] = '';
        $values['last_modified'] = time();
        $values['is_private'] = empty($values['is_private']) ? 0 : 1;

        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $project_id = $this->db->getConnection()->getLastId();

        if (! $this->board->create($project_id, $this->board->getUserColumns())) {
            $this->db->cancelTransaction();
            return false;
        }

        if ($values['is_private'] && $user_id) {
            $this->projectPermission->allowUser($project_id, $user_id);
        }

        $this->db->closeTransaction();

        return (int) $project_id;
    }

    /**
     * Check if the project have been modified
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  integer   $timestamp     Timestamp
     * @return bool
     */
    public function isModifiedSince($project_id, $timestamp)
    {
        return (bool) $this->db->table(self::TABLE)
                                ->eq('id', $project_id)
                                ->gt('last_modified', $timestamp)
                                ->count();
    }

    /**
     * Update modification date
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return bool
     */
    public function updateModificationDate($project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $project_id)->update(array(
            'last_modified' => time()
        ));
    }

    /**
     * Update a project
     *
     * @access public
     * @param  array    $values    Form values
     * @return bool
     */
    public function update(array $values)
    {
        return $this->exists($values['id']) &&
               $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);
    }

    /**
     * Remove a project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return bool
     */
    public function remove($project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $project_id)->remove();
    }

    /**
     * Return true if the project exists
     *
     * @access public
     * @param  integer    $project_id   Project id
     * @return boolean
     */
    public function exists($project_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $project_id)->count() === 1;
    }

    /**
     * Enable a project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return bool
     */
    public function enable($project_id)
    {
        return $this->exists($project_id) &&
               $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->update(array('is_active' => 1));
    }

    /**
     * Disable a project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return bool
     */
    public function disable($project_id)
    {
        return $this->exists($project_id) &&
               $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->update(array('is_active' => 0));
    }

    /**
     * Enable public access for a project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return bool
     */
    public function enablePublicAccess($project_id)
    {
        return $this->exists($project_id) &&
               $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->save(array('is_public' => 1, 'token' => Security::generateToken()));
    }

    /**
     * Disable public access for a project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return bool
     */
    public function disablePublicAccess($project_id)
    {
        return $this->exists($project_id) &&
               $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->save(array('is_public' => 0, 'token' => ''));
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
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Integer('is_active', t('This value must be an integer')),
            new Validators\Required('name', t('The project name is required')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 50), 50),
            new Validators\Unique('name', t('This project must be unique'), $this->db->getConnection(), self::TABLE),
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
        $v = new Validator($values, $this->commonValidationRules());

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
        $rules = array(
            new Validators\Required('id', t('This value is required')),
        );

        $v = new Validator($values, array_merge($rules, $this->commonValidationRules()));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Attach events
     *
     * @access public
     */
    public function attachEvents()
    {
        $events = array(
            Task::EVENT_CREATE_UPDATE,
            Task::EVENT_CLOSE,
            Task::EVENT_OPEN,
            Task::EVENT_MOVE_COLUMN,
            Task::EVENT_MOVE_POSITION,
            Task::EVENT_ASSIGNEE_CHANGE,
            GithubWebhook::EVENT_ISSUE_OPENED,
            GithubWebhook::EVENT_ISSUE_CLOSED,
            GithubWebhook::EVENT_ISSUE_REOPENED,
            GithubWebhook::EVENT_ISSUE_ASSIGNEE_CHANGE,
            GithubWebhook::EVENT_ISSUE_LABEL_CHANGE,
            GithubWebhook::EVENT_ISSUE_COMMENT,
            GithubWebhook::EVENT_COMMIT,
        );

        $listener = new ProjectModificationDate($this);

        foreach ($events as $event_name) {
            $this->event->attach($event_name, $listener);
        }
    }

    /**
     * Get project activity
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return array
     */
    public function getActivity($project_id)
    {
        $activity = array();
        $tasks = $this->taskHistory->getAllContentByProjectId($project_id, 25);
        $comments = $this->commentHistory->getAllContentByProjectId($project_id, 25);
        $subtasks = $this->subtaskHistory->getAllContentByProjectId($project_id, 25);

        foreach ($tasks as &$task) {
            $activity[$task['date_creation'].'-'.$task['id']] = $task;
        }

        foreach ($subtasks as &$subtask) {
            $activity[$subtask['date_creation'].'-'.$subtask['id']] = $subtask;
        }

        foreach ($comments as &$comment) {
            $activity[$comment['date_creation'].'-'.$comment['id']] = $comment;
        }

        krsort($activity);

        return $activity;
    }
}
