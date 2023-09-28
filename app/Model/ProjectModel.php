<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskFileModel;

/**
 * Project model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectModel extends Base
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
     * Value for private project
     *
     * @var integer
     */
    const TYPE_PRIVATE = 1;

    /**
     * Value for team project
     *
     * @var integer
     */
    const TYPE_TEAM = 0;

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
            ->columns(self::TABLE.'.*', UserModel::TABLE.'.username AS owner_username', UserModel::TABLE.'.name AS owner_name')
            ->eq(self::TABLE.'.id', $project_id)
            ->join(UserModel::TABLE, 'id', 'owner_id')
            ->findOne();
    }

    /**
     * Get a project by id with owner name and task count
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @return array
     */
    public function getByIdWithOwnerAndTaskCount($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(
                self::TABLE.'.*',
                UserModel::TABLE.'.username AS owner_username',
                UserModel::TABLE.'.name AS owner_name',
                "(SELECT count(*) FROM tasks WHERE tasks.project_id=projects.id AND tasks.is_active='1') AS nb_active_tasks"
            )
            ->eq(self::TABLE.'.id', $project_id)
            ->join(UserModel::TABLE, 'id', 'owner_id')
            ->join(TaskModel::TABLE, 'project_id', 'id')
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
     * Get a project by the email address
     *
     * @access public
     * @param  string  $email
     * @return array|boolean
     */
    public function getByEmail($email)
    {
        if (empty($email)) {
            return false;
        }

        return $this->db->table(self::TABLE)->eq('email', $email)->findOne();
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
     * @param  bool $prependNone
     * @param  bool $noPrivateProjects
     * @return array
     */
    public function getList($prependNone = true, $noPrivateProjects = true)
    {
        if ($noPrivateProjects) {
            $projects = $this->db->hashtable(self::TABLE)->eq('is_private', 0)->asc('name')->getAll('id', 'name');
        } else {
            $projects = $this->db->hashtable(self::TABLE)->asc('name')->getAll('id', 'name');
        }

        if ($prependNone) {
            return array(t('None')) + $projects;
        }

        return $projects;
    }

    /**
     * Get all projects with all its data for a given status
     *
     * @access public
     * @param  integer   $status   Project status: self::ACTIVE or self:INACTIVE
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
     * Get stats for each column of a project
     *
     * @access public
     * @param  array    $project
     * @return array
     */
    public function getColumnStats(array &$project)
    {
        $project['columns'] = $this->columnModel->getAllWithTaskCount($project['id']);
        $project['nb_active_tasks'] = 0;

        foreach ($project['columns'] as $column) {
            $project['nb_active_tasks'] += $column['nb_open_tasks'];
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
            return $this->db->table(ProjectModel::TABLE)->eq(ProjectModel::TABLE.'.id', 0);
        }

        return $this->db
            ->table(ProjectModel::TABLE)
            ->columns(self::TABLE.'.*', UserModel::TABLE.'.username AS owner_username', UserModel::TABLE.'.name AS owner_name')
            ->join(UserModel::TABLE, 'id', 'owner_id')
            ->in(self::TABLE.'.id', $project_ids)
            ->callback(array($this, 'applyColumnStats'));
    }

    /**
     * Get query for list of project without column statistics
     *
     * @access public
     * @param  array $projectIds
     * @return \PicoDb\Table
     */
    public function getQueryByProjectIds(array $projectIds)
    {
        if (empty($projectIds)) {
            return $this->db->table(ProjectModel::TABLE)->eq(ProjectModel::TABLE.'.id', 0);
        }

        return $this->db
            ->table(ProjectModel::TABLE)
            ->columns(self::TABLE.'.*', UserModel::TABLE.'.username AS owner_username', UserModel::TABLE.'.name AS owner_name')
            ->join(UserModel::TABLE, 'id', 'owner_id')
            ->in(self::TABLE.'.id', $projectIds);
    }

    /**
     * Create a project
     *
     * @access public
     * @param  array   $values Form values
     * @param  integer $userId User who create the project
     * @param  bool    $addUser Automatically add the user
     * @return int     Project id
     */
    public function create(array $values, $userId = 0, $addUser = false)
    {
        if (! empty($userId) && ! $this->userModel->exists($userId)) {
            return false;
        }

        $this->db->startTransaction();

        $values['token'] = '';
        $values['last_modified'] = time();
        $values['is_private'] = empty($values['is_private']) ? 0 : 1;
        $values['owner_id'] = $userId;

        if (! empty($values['identifier'])) {
            $values['identifier'] = strtoupper($values['identifier']);
        }

        $this->helper->model->convertIntegerFields($values, array('priority_default', 'priority_start', 'priority_end', 'task_limit'));

        if (! $this->db->table(self::TABLE)->save($values)) {
            $this->db->cancelTransaction();
            return false;
        }

        $project_id = $this->db->getLastId();

        if (! $this->boardModel->create($project_id, $this->boardModel->getUserColumns())) {
            $this->db->cancelTransaction();
            return false;
        }

        if (! $this->swimlaneModel->create($project_id, t('Default swimlane'))) {
            $this->db->cancelTransaction();
            return false;
        }

        if ($addUser && $userId) {
            $this->projectUserRoleModel->addUser($project_id, $userId, Role::PROJECT_MANAGER);
        }

        $this->categoryModel->createDefaultCategories($project_id);

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

        if (! empty($values['start_date'])) {
            $values['start_date'] = $this->dateParser->getIsoDate($values['start_date']);
        }

        if (! empty($values['end_date'])) {
            $values['end_date'] = $this->dateParser->getIsoDate($values['end_date']);
        }

        if (! empty($values['owner_id']) && ! $this->userModel->exists($values['owner_id'])) {
            return false;
        }

        $values['per_swimlane_task_limits'] = empty($values['per_swimlane_task_limits']) ? 0 : 1;

        $this->helper->model->convertIntegerFields($values, array('priority_default', 'priority_start', 'priority_end', 'task_limit'));

        $updates = $values;
        unset($updates['id']);
        return $this->exists($values['id']) &&
               $this->db->table(self::TABLE)->eq('id', $values['id'])->save($updates);
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
        // Remove all project attachments
        $this->projectFileModel->removeAll($project_id);

        // Remove all task attachments
        $file_ids = $this->db
            ->table(TaskFileModel::TABLE)
            ->eq(TaskModel::TABLE.'.project_id', $project_id)
            ->join(TaskModel::TABLE, 'id', 'task_id', TaskFileModel::TABLE)
            ->findAllByColumn(TaskFileModel::TABLE.'.id');

        foreach ($file_ids as $file_id) {
            $this->taskFileModel->remove($file_id);
        }

        // Remove project
        $this->db->table(TagModel::TABLE)->eq('project_id', $project_id)->remove();
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

    /**
     * Change usage of global tags
     *
     * @param  integer $project_id  Project id
     * @param  bool    $global_tags New global tag value
     * @return bool
     */    
    public function changeGlobalTagUsage($project_id, $global_tags)
    {
        return $this->exists($project_id) &&
               $this->db
                    ->table(self::TABLE)
                    ->eq('id', $project_id)
                    ->save(array('enable_global_tags' => $global_tags));
    }
}
