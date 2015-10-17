<?php

namespace Kanboard\Model;

use Kanboard\Event\TaskEvent;

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
     * @param  integer    $swimlane_id       Swimlane id
     * @param  boolean    $fire_events       Fire events
     * @return boolean
     */
    public function movePosition($project_id, $task_id, $column_id, $position, $swimlane_id = 0, $fire_events = true)
    {
        if ($position < 1) {
            return false;
        }

        $task = $this->taskFinder->getById($task_id);

        // Ignore closed tasks
        if ($task['is_active'] == Task::STATUS_CLOSED) {
            return true;
        }

        $result = false;

        if ($task['swimlane_id'] != $swimlane_id) {
            $result = $this->saveSwimlaneChange($project_id, $task_id, $position, $task['column_id'], $column_id, $task['swimlane_id'], $swimlane_id);
        } elseif ($task['column_id'] != $column_id) {
            $result = $this->saveColumnChange($project_id, $task_id, $position, $swimlane_id, $task['column_id'], $column_id);
        } elseif ($task['position'] != $position) {
            $result = $this->savePositionChange($project_id, $task_id, $position, $column_id, $swimlane_id);
        }

        if ($result && $fire_events) {
            $this->fireEvents($task, $column_id, $position, $swimlane_id);
        }

        return $result;
    }

    /**
     * Move a task to another swimlane
     *
     * @access private
     * @param  integer    $project_id
     * @param  integer    $task_id
     * @param  integer    $position
     * @param  integer    $original_column_id
     * @param  integer    $new_column_id
     * @param  integer    $original_swimlane_id
     * @param  integer    $new_swimlane_id
     * @return boolean
     */
    private function saveSwimlaneChange($project_id, $task_id, $position, $original_column_id, $new_column_id, $original_swimlane_id, $new_swimlane_id)
    {
        $this->db->startTransaction();
        $r1 = $this->saveTaskPositions($project_id, $task_id, 0, $original_column_id, $original_swimlane_id);
        $r2 = $this->saveTaskPositions($project_id, $task_id, $position, $new_column_id, $new_swimlane_id);
        $this->db->closeTransaction();

        return $r1 && $r2;
    }

    /**
     * Move a task to another column
     *
     * @access private
     * @param  integer    $project_id
     * @param  integer    $task_id
     * @param  integer    $position
     * @param  integer    $swimlane_id
     * @param  integer    $original_column_id
     * @param  integer    $new_column_id
     * @return boolean
     */
    private function saveColumnChange($project_id, $task_id, $position, $swimlane_id, $original_column_id, $new_column_id)
    {
        $this->db->startTransaction();
        $r1 = $this->saveTaskPositions($project_id, $task_id, 0, $original_column_id, $swimlane_id);
        $r2 = $this->saveTaskPositions($project_id, $task_id, $position, $new_column_id, $swimlane_id);
        $this->db->closeTransaction();

        return $r1 && $r2;
    }

    /**
     * Move a task to another position in the same column
     *
     * @access private
     * @param  integer    $project_id
     * @param  integer    $task_id
     * @param  integer    $position
     * @param  integer    $column_id
     * @param  integer    $swimlane_id
     * @return boolean
     */
    private function savePositionChange($project_id, $task_id, $position, $column_id, $swimlane_id)
    {
        $this->db->startTransaction();
        $result = $this->saveTaskPositions($project_id, $task_id, $position, $column_id, $swimlane_id);
        $this->db->closeTransaction();

        return $result;
    }

    /**
     * Save all task positions for one column
     *
     * @access private
     * @param  integer    $project_id
     * @param  integer    $task_id
     * @param  integer    $position
     * @param  integer    $column_id
     * @param  integer    $swimlane_id
     * @return boolean
     */
    private function saveTaskPositions($project_id, $task_id, $position, $column_id, $swimlane_id)
    {
        $tasks_ids = $this->db->table(Task::TABLE)
            ->eq('is_active', 1)
            ->eq('swimlane_id', $swimlane_id)
            ->eq('project_id', $project_id)
            ->eq('column_id', $column_id)
            ->neq('id', $task_id)
            ->asc('position')
            ->asc('id')
            ->findAllByColumn('id');

        $offset = 1;

        foreach ($tasks_ids as $current_task_id) {

            // Insert the new task
            if ($position == $offset) {
                if (! $this->saveTaskPosition($task_id, $offset, $column_id, $swimlane_id)) {
                    return false;
                }
                $offset++;
            }

            // Rewrite other tasks position
            if (! $this->saveTaskPosition($current_task_id, $offset, $column_id, $swimlane_id)) {
                return false;
            }

            $offset++;
        }

        // Insert the new task at the bottom and normalize bad position
        if ($position >= $offset && ! $this->saveTaskPosition($task_id, $offset, $column_id, $swimlane_id)) {
            return false;
        }

        return true;
    }

    /**
     * Save new task position
     *
     * @access private
     * @param  integer    $task_id
     * @param  integer    $position
     * @param  integer    $column_id
     * @param  integer    $swimlane_id
     * @return boolean
     */
    private function saveTaskPosition($task_id, $position, $column_id, $swimlane_id)
    {
        $result = $this->db->table(Task::TABLE)->eq('id', $task_id)->update(array(
            'position' => $position,
            'column_id' => $column_id,
            'swimlane_id' => $swimlane_id,
        ));

        if (! $result) {
            $this->db->cancelTransaction();
            return false;
        }

        return true;
    }

    /**
     * Fire events
     *
     * @access private
     * @param  array     $task
     * @param  integer   $new_column_id
     * @param  integer   $new_position
     * @param  integer   $new_swimlane_id
     */
    private function fireEvents(array $task, $new_column_id, $new_position, $new_swimlane_id)
    {
        $event_data = array(
            'task_id' => $task['id'],
            'project_id' => $task['project_id'],
            'position' => $new_position,
            'column_id' => $new_column_id,
            'swimlane_id' => $new_swimlane_id,
            'src_column_id' => $task['column_id'],
            'dst_column_id' => $new_column_id,
            'date_moved' => $task['date_moved'],
            'recurrence_status' => $task['recurrence_status'],
            'recurrence_trigger' => $task['recurrence_trigger'],
        );

        if ($task['swimlane_id'] != $new_swimlane_id) {
            $this->container['dispatcher']->dispatch(Task::EVENT_MOVE_SWIMLANE, new TaskEvent($event_data));
        } elseif ($task['column_id'] != $new_column_id) {
            $this->container['dispatcher']->dispatch(Task::EVENT_MOVE_COLUMN, new TaskEvent($event_data));
        } elseif ($task['position'] != $new_position) {
            $this->container['dispatcher']->dispatch(Task::EVENT_MOVE_POSITION, new TaskEvent($event_data));
        }
    }
}
