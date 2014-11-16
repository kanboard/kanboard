<?php

namespace Model;

/**
 * Task Paginator model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskPaginator extends Base
{
    /**
     * Task search with pagination
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  string     $search        Search terms
     * @param  integer    $offset        Offset
     * @param  integer    $limit         Limit
     * @param  string     $column        Sorting column
     * @param  string     $direction     Sorting direction
     * @return array
     */
    public function searchTasks($project_id, $search, $offset = 0, $limit = 25, $column = 'tasks.id', $direction = 'DESC')
    {
        return $this->taskFinder->getQuery()
                    ->eq('project_id', $project_id)
                    ->like('title', '%'.$search.'%')
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy($column, $direction)
                    ->findAll();
    }

    /**
     * Count the number of tasks for a custom search
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  string    $search       Search terms
     * @return integer
     */
    public function countSearchTasks($project_id, $search)
    {
        return $this->db->table(Task::TABLE)
                        ->eq('project_id', $project_id)
                        ->like('title', '%'.$search.'%')
                        ->count();
    }

    /**
     * Get all completed tasks with pagination
     *
     * @access public
     * @param  integer    $project_id    Project id
     * @param  integer    $offset        Offset
     * @param  integer    $limit         Limit
     * @param  string     $column        Sorting column
     * @param  string     $direction     Sorting direction
     * @return array
     */
    public function closedTasks($project_id, $offset = 0, $limit = 25, $column = 'tasks.date_completed', $direction = 'DESC')
    {
        return $this->taskFinder->getQuery()
                    ->eq('project_id', $project_id)
                    ->eq('is_active', Task::STATUS_CLOSED)
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy($column, $direction)
                    ->findAll();
    }

    /**
     * Count all closed tasks
     *
     * @access public
     * @param  integer   $project_id   Project id
     * @param  array     $status       List of status id
     * @return integer
     */
    public function countClosedTasks($project_id)
    {
        return $this->db
                    ->table(Task::TABLE)
                    ->eq('project_id', $project_id)
                    ->eq('is_active', Task::STATUS_CLOSED)
                    ->count();
    }

    /**
     * Get all open tasks for a given user
     *
     * @access public
     * @param  integer    $user_id       User id
     * @param  integer    $offset        Offset
     * @param  integer    $limit         Limit
     * @param  string     $column        Sorting column
     * @param  string     $direction     Sorting direction
     * @return array
     */
    public function userTasks($user_id, $offset = 0, $limit = 25, $column = 'tasks.id', $direction = 'ASC')
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
                    ->eq('tasks.is_active', Task::STATUS_OPEN)
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy($column, $direction)
                    ->findAll();
    }

    /**
     * Count all tasks assigned to the user
     *
     * @access public
     * @param  integer    $user_id    User id
     * @return integer
     */
    public function countUserTasks($user_id)
    {
        return $this->db
                    ->table(Task::TABLE)
                    ->eq('owner_id', $user_id)
                    ->eq('is_active', Task::STATUS_OPEN)
                    ->count();
    }
}
