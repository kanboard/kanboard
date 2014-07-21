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
     * SQL table name for users
     *
     * @var string
     */
    const TABLE_USERS = 'project_has_users';

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
     * Get a list of people that can be assigned for tasks
     *
     * @access public
     * @param  integer   $project_id            Project id
     * @param  bool      $prepend_unassigned    Prepend the 'Unassigned' value
     * @param  bool      $prepend_everybody     Prepend the 'Everbody' value
     * @return array
     */
    public function getUsersList($project_id, $prepend_unassigned = true, $prepend_everybody = false)
    {
        $allowed_users = $this->getAllowedUsers($project_id);
        $userModel = new User($this->db, $this->event);

        if (empty($allowed_users)) {
            $allowed_users = $userModel->getList();
        }

        if ($prepend_unassigned) {
            $allowed_users = array(t('Unassigned')) + $allowed_users;
        }

        if ($prepend_everybody) {
            $allowed_users = array(User::EVERYBODY_ID => t('Everybody')) + $allowed_users;
        }

        return $allowed_users;
    }

    /**
     * Get a list of allowed people for a project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return array
     */
    public function getAllowedUsers($project_id)
    {
        return $this->db
            ->table(self::TABLE_USERS)
            ->join(User::TABLE, 'id', 'user_id')
            ->eq('project_id', $project_id)
            ->asc('username')
            ->listing('user_id', 'username');
    }

    /**
     * Get allowed and not allowed users for a project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @return array
     */
    public function getAllUsers($project_id)
    {
        $users = array(
            'allowed' => array(),
            'not_allowed' => array(),
        );

        $userModel = new User($this->db, $this->event);
        $all_users = $userModel->getList();

        $users['allowed'] = $this->getAllowedUsers($project_id);

        foreach ($all_users as $user_id => $username) {

            if (! isset($users['allowed'][$user_id])) {
                $users['not_allowed'][$user_id] = $username;
            }
        }

        return $users;
    }

    /**
     * Allow a specific user for a given project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $user_id      User id
     * @return bool
     */
    public function allowUser($project_id, $user_id)
    {
        return $this->db
                    ->table(self::TABLE_USERS)
                    ->save(array('project_id' => $project_id, 'user_id' => $user_id));
    }

    /**
     * Revoke a specific user for a given project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $user_id      User id
     * @return bool
     */
    public function revokeUser($project_id, $user_id)
    {
        return $this->db
                    ->table(self::TABLE_USERS)
                    ->eq('project_id', $project_id)
                    ->eq('user_id', $user_id)
                    ->remove();
    }

    /**
     * Check if a specific user is allowed to access to a given project
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $user_id      User id
     * @return bool
     */
    public function isUserAllowed($project_id, $user_id)
    {
        // If there is nobody specified, everybody have access to the project
        $nb_users = $this->db
                    ->table(self::TABLE_USERS)
                    ->eq('project_id', $project_id)
                    ->count();

        if ($nb_users < 1) return true;

        // Check if user has admin rights
        $nb_users = $this->db
                    ->table(User::TABLE)
                    ->eq('id', $user_id)
                    ->eq('is_admin', 1)
                    ->count();

        if ($nb_users > 0) return true;

        // Otherwise, allow only specific users
        return (bool) $this->db
                    ->table(self::TABLE_USERS)
                    ->eq('project_id', $project_id)
                    ->eq('user_id', $user_id)
                    ->count();
    }

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
        return $this->db->table(self::TABLE)->eq('token', $token)->findOne();
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
     * Get all projects, optionaly fetch stats for each project and can check users permissions
     *
     * @access public
     * @param  bool       $fetch_stats          If true, return metrics about each projects
     * @param  bool       $check_permissions    If true, remove projects not allowed for the current user
     * @return array
     */
    public function getAll($fetch_stats = false, $check_permissions = false)
    {
        if (! $fetch_stats) {
            return $this->db->table(self::TABLE)->asc('name')->findAll();
        }

        $this->db->startTransaction();

        $projects = $this->db
                         ->table(self::TABLE)
                         ->asc('name')
                         ->findAll();

        $boardModel = new Board($this->db, $this->event);
        $taskModel = new Task($this->db, $this->event);
        $aclModel = new Acl($this->db, $this->event);

        foreach ($projects as $pkey => &$project) {

            if ($check_permissions && ! $this->isUserAllowed($project['id'], $aclModel->getUserId())) {
                unset($projects[$pkey]);
            }
            else {

                $columns = $boardModel->getcolumns($project['id']);
                $project['nb_active_tasks'] = 0;

                foreach ($columns as &$column) {
                    $column['nb_active_tasks'] = $taskModel->countByColumnId($project['id'], $column['id']);
                    $project['nb_active_tasks'] += $column['nb_active_tasks'];
                }

                $project['columns'] = $columns;
                $project['nb_tasks'] = $taskModel->countByProjectId($project['id']);
                $project['nb_inactive_tasks'] = $project['nb_tasks'] - $project['nb_active_tasks'];
            }
        }

        $this->db->closeTransaction();

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
     * Filter a list of projects for a given user
     *
     * @access public
     * @param  array     $projects     Project list: ['project_id' => 'project_name']
     * @param  integer   $user_id      User id
     * @return array
     */
    public function filterListByAccess(array $projects, $user_id)
    {
        foreach ($projects as $project_id => $project_name) {
            if (! $this->isUserAllowed($project_id, $user_id)) {
                unset($projects[$project_id]);
            }
        }

        return $projects;
    }

    /**
     * Return a list of projects for a given user
     *
     * @access public
     * @param  integer   $user_id      User id
     * @return array
     */
    public function getAvailableList($user_id)
    {
        return $this->filterListByAccess($this->getListByStatus(self::ACTIVE), $user_id);
    }

    /**
     * Create a project
     *
     * @access public
     * @param  array    $values   Form values
     * @return integer            Project id
     */
    public function create(array $values)
    {
        $this->db->startTransaction();

        $values['token'] = Security::generateToken();

        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $project_id = $this->db->getConnection()->getLastId();

        $boardModel = new Board($this->db, $this->event);
        $boardModel->create($project_id, array(
            t('Backlog'),
            t('Ready'),
            t('Work in progress'),
            t('Done'),
        ));

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
        return $this->db->table(self::TABLE)->eq('id', $project_id)->save(array(
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
        return $this->db->table(self::TABLE)->eq('id', $values['id'])->save($values);
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
     * Enable a project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return bool
     */
    public function enable($project_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->save(array('is_active' => 1));
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
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->save(array('is_active' => 0));
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
        $v = new Validator($values, array(
            new Validators\Required('name', t('The project name is required')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 50), 50),
            new Validators\Unique('name', t('This project must be unique'), $this->db->getConnection(), self::TABLE)
        ));

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
        $v = new Validator($values, array(
            new Validators\Required('id', t('The project id is required')),
            new Validators\Integer('id', t('This value must be an integer')),
            new Validators\Required('name', t('The project name is required')),
            new Validators\MaxLength('name', t('The maximum length is %d characters', 50), 50),
            new Validators\Unique('name', t('This project must be unique'), $this->db->getConnection(), self::TABLE),
            new Validators\Integer('is_active', t('This value must be an integer'))
        ));

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }

    /**
     * Validate allowed users
     *
     * @access public
     * @param  array   $values           Form values
     * @return array   $valid, $errors   [0] = Success or not, [1] = List of errors
     */
    public function validateUserAccess(array $values)
    {
        $v = new Validator($values, array(
            new Validators\Required('project_id', t('The project id is required')),
            new Validators\Integer('project_id', t('This value must be an integer')),
            new Validators\Required('user_id', t('The user id is required')),
            new Validators\Integer('user_id', t('This value must be an integer')),
        ));

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
            Task::EVENT_UPDATE,
            Task::EVENT_CREATE,
            Task::EVENT_CLOSE,
            Task::EVENT_OPEN,
        );

        $listener = new ProjectModificationDate($this);

        foreach ($events as $event_name) {
            $this->event->attach($event_name, $listener);
        }
    }
}
