<?php

namespace Kanboard\Model;

use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;

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
     * Get a project by id with owner name
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getByIdWithOwner($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(self::TABLE.'.*', User::TABLE.'.username AS owner_username', User::TABLE.'.name AS owner_name')
            ->eq(self::TABLE.'.id', $project_id)
            ->join(User::TABLE, 'id', 'owner_id')
            ->findOne();
    }

    /**
     * Get a project by the name
     *
     * @access public
     * @param  string     $name    Project name
     * @return array
     */
    public function getByName($name)
    {
        return $this->db->table(self::TABLE)->eq('name', $name)->findOne();
    }

    /**
     * Get a project by the identifier (code)
     *
     * @access public
     * @param  string  $identifier
     * @return array|boolean
     */
    public function getByIdentifier($identifier)
    {
        if (empty($identifier)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('identifier', strtoupper($identifier))->findOne();
    }

    /**
     * Fetch project data by using the token
     *
     * @access public
     * @param  string   $token    Token
     * @return array|boolean
     */
    public function getByToken($token)
    {
        if (empty($token)) {
            return false;
        }

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
        return $this->db->table(self::TABLE)->eq('id', $project_id)->eq('is_private', 1)->exists();
    }

    /**
     * Get all projects
     *
     * @access public
     * @return array
     */
    public function getAll()
    {
        return $this->db->table(self::TABLE)->asc('name')->findAll();
    }

    /**
     * Get all projects with given Ids
     *
     * @access public
     * @param  integer[]   $project_ids
     * @return array
     */
    public function getAllByIds(array $project_ids)
    {
        if (empty($project_ids)) {
            return array();
        }

        return $this->db->table(self::TABLE)->in('id', $project_ids)->asc('name')->findAll();
    }

    /**
     * Get all project ids
     *
     * @access public
     * @return array
     */
    public function getAllIds()
    {
        return $this->db->table(self::TABLE)->asc('name')->findAllByColumn('id');
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
            return array(t('None')) + $this->db->hashtable(self::TABLE)->asc('name')->getAll('id', 'name');
        }

        return $this->db->hashtable(self::TABLE)->asc('name')->getAll('id', 'name');
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
                    ->hashtable(self::TABLE)
                    ->asc('name')
                    ->eq('is_active', $status)
                    ->getAll('id', 'name');
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
    public function getTaskStats($project_id)
    {
        $stats = array();
        $stats['nb_active_tasks'] = 0;
        $columns = $this->column->getAll($project_id);
        $column_stats = $this->board->getColumnStats($project_id);

        foreach ($columns as &$column) {
            $column['nb_active_tasks'] = isset($column_stats[$column['id']]) ? $column_stats[$column['id']] : 0;
            $stats['nb_active_tasks'] += $column['nb_active_tasks'];
        }

        $stats['columns'] = $columns;
        $stats['nb_tasks'] = $this->taskFinder->countByProjectId($project_id);
        $stats['nb_inactive_tasks'] = $stats['nb_tasks'] - $stats['nb_active_tasks'];

        return $stats;
    }

    /**
     * Get stats for each column of a project
     *
     * @access public
     * @param  array    $project
     * @return array
     */
    public function getColumnStats(array &$project)
    {
        $project['columns'] = $this->column->getAll($project['id']);
        $stats = $this->board->getColumnStats($project['id']);

        foreach ($project['columns'] as &$column) {
            $column['nb_tasks'] = isset($stats[$column['id']]) ? $stats[$column['id']] : 0;
        }

        return $project;
    }

    /**
     * Apply column stats to a collection of projects (filter callback)
     *
     * @access public
     * @param  array    $projects
     * @return array
     */
    public function applyColumnStats(array $projects)
    {
        foreach ($projects as &$project) {
            $this->getColumnStats($project);
        }

        return $projects;
    }

    /**
     * Get project summary for a list of project
     *
     * @access public
     * @param  array      $project_ids     List of project id
     * @return \PicoDb\Table
     */
    public function getQueryColumnStats(array $project_ids)
    {
        if (empty($project_ids)) {
            return $this->db->table(Project::TABLE)->limit(0);
        }

        return $this->db
                    ->table(Project::TABLE)
                    ->columns(self::TABLE.'.*', User::TABLE.'.username AS owner_username', User::TABLE.'.name AS owner_name')
                    ->join(User::TABLE, 'id', 'owner_id')
                    ->in(self::TABLE.'.id', $project_ids)
                    ->callback(array($this, 'applyColumnStats'));
    }

    /**
     * Create a project
     *
     * @access public
     * @param  array    $values     Form values
     * @param  integer  $user_id    User who create the project
     * @param  bool     $add_user   Automatically add the user
     * @return integer              Project id
     */
    public function create(array $values, $user_id = 0, $add_user = false)
    {
        $this->db->startTransaction();

        $values['token'] = '';
        $values['last_modified'] = time();
        $values['is_private'] = empty($values['is_private']) ? 0 : 1;
        $values['owner_id'] = $user_id;

        if (! empty($values['identifier'])) {
            $values['identifier'] = strtoupper($values['identifier']);
        }

        $this->helper->model->convertIntegerFields($values, array('priority_default', 'priority_start', 'priority_end'));

        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $project_id = $this->db->getLastId();

        if (! $this->board->create($project_id, $this->board->getUserColumns())) {
            $this->db->cancelTransaction();
            return false;
        }

        if ($add_user && $user_id) {
            $this->projectUserRole->addUser($project_id, $user_id, Role::PROJECT_MANAGER);
        }

        $this->category->createDefaultCategories($project_id);

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
        if (! empty($values['identifier'])) {
            $values['identifier'] = strtoupper($values['identifier']);
        }

        $this->helper->model->convertIntegerFields($values, array('priority_default', 'priority_start', 'priority_end'));

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
        return $this->db->table(self::TABLE)->eq('id', $project_id)->exists();
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
                    ->save(array('is_public' => 1, 'token' => Token::getToken()));
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
}
