<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Task Finder model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TaskFinderModel extends Base
{
    /**
     * Get query for project user overview
     *
     * @access public
     * @param array    $project_ids
     * @param integer  $is_active
     * @return \PicoDb\Table
     */
    public function getProjectUserOverviewQuery(array $project_ids, $is_active)
    {
        if (empty($project_ids)) {
            $project_ids = array(-1);
        }

        return $this->db
                    ->table(TaskModel::TABLE)
                    ->columns(
                        TaskModel::TABLE.'.id',
                        TaskModel::TABLE.'.title',
                        TaskModel::TABLE.'.date_due',
                        TaskModel::TABLE.'.date_started',
                        TaskModel::TABLE.'.project_id',
                        TaskModel::TABLE.'.color_id',
                        TaskModel::TABLE.'.priority',
                        TaskModel::TABLE.'.time_spent',
                        TaskModel::TABLE.'.time_estimated',
                        ProjectModel::TABLE.'.name AS project_name',
                        ColumnModel::TABLE.'.title AS column_name',
                        UserModel::TABLE.'.username AS assignee_username',
                        UserModel::TABLE.'.name AS assignee_name'
                    )
                    ->eq(TaskModel::TABLE.'.is_active', $is_active)
                    ->in(ProjectModel::TABLE.'.id', $project_ids)
                    ->join(ProjectModel::TABLE, 'id', 'project_id')
                    ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                    ->join(UserModel::TABLE, 'id', 'owner_id', TaskModel::TABLE);
    }

    /**
     * Get query for assigned user tasks
     *
     * @access public
     * @param  integer    $user_id       User id
     * @return \PicoDb\Table
     */
    public function getUserQuery($user_id)
    {
        return $this->getExtendedQuery()
                    ->beginOr()
                    ->eq(TaskModel::TABLE.'.owner_id', $user_id)
                    ->addCondition(TaskModel::TABLE.".id IN (SELECT task_id FROM ".SubtaskModel::TABLE." WHERE ".SubtaskModel::TABLE.".user_id='$user_id')")
                    ->closeOr()
                    ->eq(TaskModel::TABLE.'.is_active', TaskModel::STATUS_OPEN)
                    ->eq(ProjectModel::TABLE.'.is_active', ProjectModel::ACTIVE)
                    ->eq(ColumnModel::TABLE.'.hide_in_dashboard', 0);
    }

    /**
     * Extended query
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getExtendedQuery()
    {
        return $this->db
            ->table(TaskModel::TABLE)
            ->columns(
                '(SELECT COUNT(*) FROM '.CommentModel::TABLE.' WHERE task_id=tasks.id) AS nb_comments',
                '(SELECT COUNT(*) FROM '.TaskFileModel::TABLE.' WHERE task_id=tasks.id) AS nb_files',
                '(SELECT COUNT(*) FROM '.SubtaskModel::TABLE.' WHERE '.SubtaskModel::TABLE.'.task_id=tasks.id) AS nb_subtasks',
                '(SELECT COUNT(*) FROM '.SubtaskModel::TABLE.' WHERE '.SubtaskModel::TABLE.'.task_id=tasks.id AND status=2) AS nb_completed_subtasks',
                '(SELECT COUNT(*) FROM '.TaskLinkModel::TABLE.' WHERE '.TaskLinkModel::TABLE.'.task_id = tasks.id) AS nb_links',
                '(SELECT COUNT(*) FROM '.TaskExternalLinkModel::TABLE.' WHERE '.TaskExternalLinkModel::TABLE.'.task_id = tasks.id) AS nb_external_links',
                '(SELECT DISTINCT 1 FROM '.TaskLinkModel::TABLE.' WHERE '.TaskLinkModel::TABLE.'.task_id = tasks.id AND '.TaskLinkModel::TABLE.'.link_id = 9) AS is_milestone',
                TaskModel::TABLE.'.id',
                TaskModel::TABLE.'.reference',
                TaskModel::TABLE.'.title',
                TaskModel::TABLE.'.description',
                TaskModel::TABLE.'.date_creation',
                TaskModel::TABLE.'.date_modification',
                TaskModel::TABLE.'.date_completed',
                TaskModel::TABLE.'.date_started',
                TaskModel::TABLE.'.date_due',
                TaskModel::TABLE.'.color_id',
                TaskModel::TABLE.'.project_id',
                TaskModel::TABLE.'.column_id',
                TaskModel::TABLE.'.swimlane_id',
                TaskModel::TABLE.'.owner_id',
                TaskModel::TABLE.'.creator_id',
                TaskModel::TABLE.'.position',
                TaskModel::TABLE.'.is_active',
                TaskModel::TABLE.'.score',
                TaskModel::TABLE.'.category_id',
                TaskModel::TABLE.'.priority',
                TaskModel::TABLE.'.date_moved',
                TaskModel::TABLE.'.recurrence_status',
                TaskModel::TABLE.'.recurrence_trigger',
                TaskModel::TABLE.'.recurrence_factor',
                TaskModel::TABLE.'.recurrence_timeframe',
                TaskModel::TABLE.'.recurrence_basedate',
                TaskModel::TABLE.'.recurrence_parent',
                TaskModel::TABLE.'.recurrence_child',
                TaskModel::TABLE.'.time_estimated',
                TaskModel::TABLE.'.time_spent',
                TaskModel::TABLE.'.reference',
                UserModel::TABLE.'.username AS assignee_username',
                UserModel::TABLE.'.name AS assignee_name',
                UserModel::TABLE.'.email AS assignee_email',
                UserModel::TABLE.'.avatar_path AS assignee_avatar_path',
                CategoryModel::TABLE.'.name AS category_name',
                CategoryModel::TABLE.'.description AS category_description',
                CategoryModel::TABLE.'.color_id AS category_color_id',
                ColumnModel::TABLE.'.title AS column_name',
                ColumnModel::TABLE.'.position AS column_position',
                SwimlaneModel::TABLE.'.name AS swimlane_name',
                ProjectModel::TABLE.'.name AS project_name'
            )
            ->join(UserModel::TABLE, 'id', 'owner_id', TaskModel::TABLE)
            ->left(UserModel::TABLE, 'uc', 'id', TaskModel::TABLE, 'creator_id')
            ->join(CategoryModel::TABLE, 'id', 'category_id', TaskModel::TABLE)
            ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
            ->join(SwimlaneModel::TABLE, 'id', 'swimlane_id', TaskModel::TABLE)
            ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE);
    }

    /**
     * Get all tasks for a given project and status
     *
     * @access public
     * @param  integer   $project_id      Project id
     * @param  integer   $status_id       Status id
     * @return array
     */
    public function getAll($project_id, $status_id = TaskModel::STATUS_OPEN)
    {
        return $this->db
                    ->table(TaskModel::TABLE)
                    ->eq(TaskModel::TABLE.'.project_id', $project_id)
                    ->eq(TaskModel::TABLE.'.is_active', $status_id)
                    ->asc(TaskModel::TABLE.'.id')
                    ->findAll();
    }

    /**
     * Get all tasks for a given project and status
     *
     * @access public
     * @param  integer   $project_id
     * @param  array     $status
     * @return array
     */
    public function getAllIds($project_id, array $status = array(TaskModel::STATUS_OPEN))
    {
        return $this->db
                    ->table(TaskModel::TABLE)
                    ->eq(TaskModel::TABLE.'.project_id', $project_id)
                    ->in(TaskModel::TABLE.'.is_active', $status)
                    ->asc(TaskModel::TABLE.'.id')
                    ->findAllByColumn(TaskModel::TABLE.'.id');
    }

    /**
     * Get overdue tasks query
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getOverdueTasksQuery()
    {
        return $this->db->table(TaskModel::TABLE)
                    ->columns(
                        TaskModel::TABLE.'.id',
                        TaskModel::TABLE.'.title',
                        TaskModel::TABLE.'.date_due',
                        TaskModel::TABLE.'.project_id',
                        TaskModel::TABLE.'.creator_id',
                        TaskModel::TABLE.'.owner_id',
                        ProjectModel::TABLE.'.name AS project_name',
                        UserModel::TABLE.'.username AS assignee_username',
                        UserModel::TABLE.'.name AS assignee_name'
                    )
                    ->join(ProjectModel::TABLE, 'id', 'project_id')
                    ->join(UserModel::TABLE, 'id', 'owner_id')
                    ->eq(ProjectModel::TABLE.'.is_active', 1)
                    ->eq(TaskModel::TABLE.'.is_active', 1)
                    ->neq(TaskModel::TABLE.'.date_due', 0)
                    ->lte(TaskModel::TABLE.'.date_due', time());
    }

    /**
     * Get a list of overdue tasks for all projects
     *
     * @access public
     * @return array
     */
    public function getOverdueTasks()
    {
        return $this->getOverdueTasksQuery()->findAll();
    }

     /**
     * Get a list of overdue tasks by project
     *
     * @access public
     * @param  integer $project_id
     * @return array
     */
    public function getOverdueTasksByProject($project_id)
    {
        return $this->getOverdueTasksQuery()->eq(TaskModel::TABLE.'.project_id', $project_id)->findAll();
    }

     /**
     * Get a list of overdue tasks by user
     *
     * @access public
     * @param  integer $user_id
     * @return array
     */
    public function getOverdueTasksByUser($user_id)
    {
        return $this->getOverdueTasksQuery()->eq(TaskModel::TABLE.'.owner_id', $user_id)->findAll();
    }

    /**
     * Get project id for a given task
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return integer
     */
    public function getProjectId($task_id)
    {
        return (int) $this->db->table(TaskModel::TABLE)->eq('id', $task_id)->findOneColumn('project_id') ?: 0;
    }

    /**
     * Fetch a task by the id
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return array
     */
    public function getById($task_id)
    {
        return $this->db->table(TaskModel::TABLE)->eq('id', $task_id)->findOne();
    }

    /**
     * Fetch a task by the reference (external id)
     *
     * @access public
     * @param  integer  $project_id  Project id
     * @param  string   $reference   Task reference
     * @return array
     */
    public function getByReference($project_id, $reference)
    {
        return $this->db->table(TaskModel::TABLE)->eq('project_id', $project_id)->eq('reference', $reference)->findOne();
    }

    /**
     * Get task details (fetch more information from other tables)
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return array
     */
    public function getDetails($task_id)
    {
        return $this->db->table(TaskModel::TABLE)
            ->columns(
                TaskModel::TABLE.'.*',
                CategoryModel::TABLE.'.name AS category_name',
                SwimlaneModel::TABLE.'.name AS swimlane_name',
                ProjectModel::TABLE.'.name AS project_name',
                ColumnModel::TABLE.'.title AS column_title',
                UserModel::TABLE.'.username AS assignee_username',
                UserModel::TABLE.'.name AS assignee_name',
                'uc.username AS creator_username',
                'uc.name AS creator_name',
                CategoryModel::TABLE.'.description AS category_description',
                ColumnModel::TABLE.'.position AS column_position'
            )
            ->join(UserModel::TABLE, 'id', 'owner_id', TaskModel::TABLE)
            ->left(UserModel::TABLE, 'uc', 'id', TaskModel::TABLE, 'creator_id')
            ->join(CategoryModel::TABLE, 'id', 'category_id', TaskModel::TABLE)
            ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
            ->join(SwimlaneModel::TABLE, 'id', 'swimlane_id', TaskModel::TABLE)
            ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
            ->eq(TaskModel::TABLE.'.id', $task_id)
            ->findOne();
    }

    /**
     * Get iCal query
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getICalQuery()
    {
        return $this->db->table(TaskModel::TABLE)
            ->left(UserModel::TABLE, 'ua', 'id', TaskModel::TABLE, 'owner_id')
            ->left(UserModel::TABLE, 'uc', 'id', TaskModel::TABLE, 'creator_id')
            ->columns(
                TaskModel::TABLE.'.*',
                'ua.email AS assignee_email',
                'ua.name AS assignee_name',
                'ua.username AS assignee_username',
                'uc.email AS creator_email',
                'uc.name AS creator_name',
                'uc.username AS creator_username'
            );
    }

    /**
     * Count all tasks for a given project and status
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  array     $status       List of status id
     * @return integer
     */
    public function countByProjectId($project_id, array $status = array(TaskModel::STATUS_OPEN, TaskModel::STATUS_CLOSED))
    {
        return $this->db
                    ->table(TaskModel::TABLE)
                    ->eq('project_id', $project_id)
                    ->in('is_active', $status)
                    ->count();
    }

    /**
     * Count the number of tasks for a given column and status
     *
     * @access public
     * @param  integer $project_id Project id
     * @param  integer $column_id Column id
     * @param  array   $status
     * @return int
     */
    public function countByColumnId($project_id, $column_id, array $status = array(TaskModel::STATUS_OPEN))
    {
        return $this->db
                    ->table(TaskModel::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->in('is_active', $status)
                    ->count();
    }

    /**
     * Count the number of tasks for a given column and swimlane
     *
     * @access public
     * @param  integer   $project_id     Project id
     * @param  integer   $column_id      Column id
     * @param  integer   $swimlane_id    Swimlane id
     * @return integer
     */
    public function countByColumnAndSwimlaneId($project_id, $column_id, $swimlane_id)
    {
        return $this->db
                    ->table(TaskModel::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->eq('swimlane_id', $swimlane_id)
                    ->eq('is_active', 1)
                    ->count();
    }

    /**
     * Return true if the task exists
     *
     * @access public
     * @param  integer    $task_id   Task id
     * @return boolean
     */
    public function exists($task_id)
    {
        return $this->db->table(TaskModel::TABLE)->eq('id', $task_id)->exists();
    }

    /**
     * Get project token
     *
     * @access public
     * @param  integer $task_id
     * @return string
     */
    public function getProjectToken($task_id)
    {
        return $this->db
            ->table(TaskModel::TABLE)
            ->eq(TaskModel::TABLE.'.id', $task_id)
            ->join(ProjectModel::TABLE, 'id', 'project_id')
            ->findOneColumn(ProjectModel::TABLE.'.token');
    }
}
