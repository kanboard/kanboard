<?php

namespace Model;

/**
 * Subtask Paginator
 *
 * @package  model
 * @author   Frederic Guillot
 */
class SubtaskPaginator extends Base
{
    /**
     * Get all subtasks assigned to a user
     *
     * @access public
     * @param  integer    $user_id         User id
     * @param  array      $status          List of status
     * @param  integer    $offset          Offset
     * @param  integer    $limit           Limit
     * @param  string     $column          Sorting column
     * @param  string     $direction       Sorting direction
     * @return array
     */
    public function userSubtasks($user_id, array $status, $offset = 0, $limit = 25, $column = 'tasks.id', $direction = 'asc')
    {
        $status_list = $this->subTask->getStatusList();

        $subtasks = $this->db->table(SubTask::TABLE)
                             ->columns(
                                SubTask::TABLE.'.*',
                                Task::TABLE.'.project_id',
                                Task::TABLE.'.color_id',
                                Project::TABLE.'.name AS project_name'
                             )
                             ->eq('user_id', $user_id)
                             ->in(SubTask::TABLE.'.status', $status)
                             ->join(Task::TABLE, 'id', 'task_id')
                             ->join(Project::TABLE, 'id', 'project_id', Task::TABLE)
                             ->offset($offset)
                             ->limit($limit)
                             ->orderBy($column, $direction)
                             ->findAll();

        foreach ($subtasks as &$subtask) {
            $subtask['status_name'] = $status_list[$subtask['status']];
        }

        return $subtasks;
    }

    /**
     * Count all subtasks assigned to the user
     *
     * @access public
     * @param  integer    $user_id         User id
     * @param  array      $status          List of status
     * @return integer
     */
    public function countUserSubtasks($user_id, array $status)
    {
        return $this->db
                    ->table(SubTask::TABLE)
                    ->eq('user_id', $user_id)
                    ->in('status', $status)
                    ->count();
    }
}
