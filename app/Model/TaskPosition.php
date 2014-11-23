<?php

namespace Model;

/**
 * Task Position
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskPosition extends Base
{
    /**
     * Move a task to another column or to another position
     *
     * @access public
     * @param  integer    $project_id        Project id
     * @param  integer    $task_id           Task id
     * @param  integer    $column_id         Column id
     * @param  integer    $position          Position (must be >= 1)
     * @return boolean
     */
    public function movePosition($project_id, $task_id, $column_id, $position)
    {
        $original_task = $this->taskFinder->getById($task_id);
        $positions = $this->calculatePositions($project_id, $task_id, $column_id, $position);

        if ($positions === false || ! $this->savePositions($positions)) {
            return false;
        }

        $this->fireEvents($original_task, $column_id, $position);

        return true;
    }

    /**
     * Calculate the new position of all tasks
     *
     * @access public
     * @param  integer    $project_id        Project id
     * @param  integer    $task_id           Task id
     * @param  integer    $column_id         Column id
     * @param  integer    $position          Position (must be >= 1)
     * @return array|boolean
     */
    public function calculatePositions($project_id, $task_id, $column_id, $position)
    {
        // The position can't be lower than 1
        if ($position < 1) {
            return false;
        }

        $board = $this->db->table(Board::TABLE)->eq('project_id', $project_id)->asc('position')->findAllByColumn('id');
        $columns = array();

        // For each column fetch all tasks ordered by position
        foreach ($board as $board_column_id) {

            $columns[$board_column_id] = $this->db->table(Task::TABLE)
                          ->eq('is_active', 1)
                          ->eq('project_id', $project_id)
                          ->eq('column_id', $board_column_id)
                          ->neq('id', $task_id)
                          ->asc('position')
                          ->findAllByColumn('id');
        }

        // The column must exists
        if (! isset($columns[$column_id])) {
            return false;
        }

        // We put our task to the new position
        array_splice($columns[$column_id], $position - 1, 0, $task_id);

        return $columns;
    }

    /**
     * Save task positions
     *
     * @access private
     * @param  array       $columns          Sorted tasks
     * @return boolean
     */
    private function savePositions(array $columns)
    {
        return $this->db->transaction(function ($db) use ($columns) {

            foreach ($columns as $column_id => $column) {

                $position = 1;

                foreach ($column as $task_id) {

                    $result = $db->table(Task::TABLE)->eq('id', $task_id)->update(array(
                        'position' => $position,
                        'column_id' => $column_id
                    ));

                    if (! $result) {
                        return false;
                    }

                    $position++;
                }
            }
        });
    }

    /**
     * Fire events
     *
     * @access public
     * @param  array     $task
     * @param  integer   $new_column_id
     * @param  integer   $new_position
     */
    public function fireEvents(array $task, $new_column_id, $new_position)
    {
        $event_data = array(
            'task_id' => $task['id'],
            'project_id' => $task['project_id'],
            'position' => $new_position,
            'column_id' => $new_column_id,
        );

        if ($task['column_id'] != $new_column_id) {
            $this->event->trigger(Task::EVENT_MOVE_COLUMN, $event_data);
        }
        else if ($task['position'] != $new_position) {
            $this->event->trigger(Task::EVENT_MOVE_POSITION, $event_data);
        }
    }
}
