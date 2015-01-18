<?php

namespace Model;

use PDO;

/**
 * Task Finder model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskFinder extends Base
{
    /**
     * Get query for closed tasks
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @return \PicoDb\Table
     */
    public function getClosedTaskQuery($project_id)
    {
        return $this->getExtendedQuery()
                    ->eq('project_id', $project_id)
                    ->eq('is_active', Task::STATUS_CLOSED);
    }

    /**
     * Get query for task search
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $search        Search terms
     * @return \PicoDb\Table
     */
    public function getSearchQuery($project_id, $search)
    {
        return $this->getExtendedQuery()
                    ->eq('project_id', $project_id)
                    ->ilike('title', '%'.$search.'%');
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
        return $this->db
                    ->table(Task::TABLE)
                    ->columns(
                        'tasks.id',
                        'tasks.title',
                        'tasks.date_due',
                        'tasks.date_creation',
                        'tasks.project_id',
                        'tasks.color_id',
                        'projects.name AS project_name'
                    )
                    ->join(Project::TABLE, 'id', 'project_id')
                    ->eq('tasks.owner_id', $user_id)
                    ->eq('tasks.is_active', Task::STATUS_OPEN);
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
            ->table(Task::TABLE)
            ->columns(
                '(SELECT count(*) FROM comments WHERE task_id=tasks.id) AS nb_comments',
                '(SELECT count(*) FROM task_has_files WHERE task_id=tasks.id) AS nb_files',
                '(SELECT count(*) FROM task_has_subtasks WHERE task_id=tasks.id) AS nb_subtasks',
                '(SELECT count(*) FROM task_has_subtasks WHERE task_id=tasks.id AND status=2) AS nb_completed_subtasks',
                'tasks.id',
                'tasks.reference',
                'tasks.title',
                'tasks.description',
                'tasks.date_creation',
                'tasks.date_modification',
                'tasks.date_completed',
                'tasks.date_due',
                'tasks.color_id',
                'tasks.project_id',
                'tasks.column_id',
                'tasks.swimlane_id',
                'tasks.owner_id',
                'tasks.creator_id',
                'tasks.position',
                'tasks.is_active',
                'tasks.score',
                'tasks.category_id',
                'users.username AS assignee_username',
                'users.name AS assignee_name'
            )
            ->join(User::TABLE, 'id', 'owner_id');
    }

    /**
     * Get all tasks shown on the board (sorted by position)
     *
     * @access public
     * @param  integer    $project_id     Project id
     * @param  integer    $column_id      Column id
     * @param  integer    $swimlane_id    Swimlane id
     * @return array
     */
    public function getTasksByColumnAndSwimlane($project_id, $column_id, $swimlane_id = 0)
    {
        return $this->getExtendedQuery()
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->eq('swimlane_id', $swimlane_id)
                    ->eq('is_active', Task::STATUS_OPEN)
                    ->asc('tasks.position')
                    ->findAll();
    }

    /**
     * Get all tasks for a given project and status
     *
     * @access public
     * @param  integer   $project_id      Project id
     * @param  integer   $status_id       Status id
     * @return array
     */
    public function getAll($project_id, $status_id = Task::STATUS_OPEN)
    {
        return $this->db
                    ->table(Task::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('is_active', $status_id)
                    ->findAll();
    }

    /**
     * Get a list of overdue tasks for all projects
     *
     * @access public
     * @return array
     */
    public function getOverdueTasks()
    {
        $tasks = $this->db->table(Task::TABLE)
                    ->columns(
                        Task::TABLE.'.id',
                        Task::TABLE.'.title',
                        Task::TABLE.'.date_due',
                        Task::TABLE.'.project_id',
                        Project::TABLE.'.name AS project_name',
                        User::TABLE.'.username AS assignee_username',
                        User::TABLE.'.name AS assignee_name'
                    )
                    ->join(Project::TABLE, 'id', 'project_id')
                    ->join(User::TABLE, 'id', 'owner_id')
                    ->eq(Project::TABLE.'.is_active', 1)
                    ->eq(Task::TABLE.'.is_active', 1)
                    ->neq(Task::TABLE.'.date_due', 0)
                    ->lte(Task::TABLE.'.date_due', mktime(23, 59, 59))
                    ->findAll();

        return $tasks;
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
        return $this->db->table(Task::TABLE)->eq('id', $task_id)->findOne();
    }

    /**
     * Fetch a task by the reference (external id)
     *
     * @access public
     * @param  string   $reference   Task reference
     * @return array
     */
    public function getByReference($reference)
    {
        return $this->db->table(Task::TABLE)->eq('reference', $reference)->findOne();
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
        $sql = '
            SELECT
            tasks.id,
            tasks.reference,
            tasks.title,
            tasks.description,
            tasks.date_creation,
            tasks.date_completed,
            tasks.date_modification,
            tasks.date_due,
            tasks.date_started,
            tasks.time_estimated,
            tasks.time_spent,
            tasks.color_id,
            tasks.project_id,
            tasks.column_id,
            tasks.owner_id,
            tasks.creator_id,
            tasks.position,
            tasks.is_active,
            tasks.score,
            tasks.category_id,
            tasks.swimlane_id,
            project_has_categories.name AS category_name,
            projects.name AS project_name,
            columns.title AS column_title,
            users.username AS assignee_username,
            users.name AS assignee_name,
            creators.username AS creator_username,
            creators.name AS creator_name
            FROM tasks
            LEFT JOIN users ON users.id = tasks.owner_id
            LEFT JOIN users AS creators ON creators.id = tasks.creator_id
            LEFT JOIN project_has_categories ON project_has_categories.id = tasks.category_id
            LEFT JOIN projects ON projects.id = tasks.project_id
            LEFT JOIN columns ON columns.id = tasks.column_id
            WHERE tasks.id = ?
        ';

        $rq = $this->db->execute($sql, array($task_id));
        return $rq->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Count all tasks for a given project and status
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  array     $status       List of status id
     * @return integer
     */
    public function countByProjectId($project_id, array $status = array(Task::STATUS_OPEN, Task::STATUS_CLOSED))
    {
        return $this->db
                    ->table(Task::TABLE)
                    ->eq('project_id', $project_id)
                    ->in('is_active', $status)
                    ->count();
    }

    /**
     * Count the number of tasks for a given column and status
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  integer   $column_id    Column id
     * @return integer
     */
    public function countByColumnId($project_id, $column_id)
    {
        return $this->db
                    ->table(Task::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->in('is_active', 1)
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
                    ->table(Task::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('column_id', $column_id)
                    ->eq('swimlane_id', $swimlane_id)
                    ->in('is_active', 1)
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
        return $this->db->table(Task::TABLE)->eq('id', $task_id)->count() === 1;
    }
}
