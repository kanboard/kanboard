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
        $result = false;

        if ($position < 1) {
            return $result;
        }

        $task = $this->taskFinder->getById($task_id);

        if ($task['is_active'] == Task::STATUS_CLOSED) {
            return true;
        }

        $this->db->startTransaction();

        if ($task['swimlane_id'] != $swimlane_id || $task['column_id'] != $column_id) {
            $result = $this->moveTaskToAnotherColumn($task, $swimlane_id, $column_id, $position);
        } elseif ($task['position'] != $position) {
            $result = $this->moveTaskWithinSameColumn($task, $position);
        }

        if (! $result) {
            $this->db->cancelTransaction();
            return false;
        }

        $this->db->closeTransaction();

        if ($fire_events) {
            $this->fireEvents($task, $column_id, $position, $swimlane_id);
        }

        return $result;
    }

    /**
     * Move a task to another column/swimlane
     *
     * @access private
     * @param  array   $task
     * @param  integer $swimlane_id
     * @param  integer $column_id
     * @param  integer $position
     * @return boolean
     */
    private function moveTaskToAnotherColumn(array $task, $swimlane_id, $column_id, $position)
    {
        $results = array();
        $max = $this->getQuery($task['project_id'], $swimlane_id, $column_id)->count();
        $position = $max > 0 && $position > $max ? $max + 1 : $position;

        $results[] = $this->getQuery($task['project_id'], $task['swimlane_id'], $task['column_id'])->gt('position', $task['position'])->decrement('position', 1);
        $results[] = $this->getQuery($task['project_id'], $swimlane_id, $column_id)->gte('position', $position)->increment('position', 1);
        $results[] = $this->updateTaskPosition($task['id'], $swimlane_id, $column_id, $position);

        return !in_array(false, $results, true);
    }

    /**
     * Move a task within the same column
     *
     * @access private
     * @param  array   $task
     * @param  integer $position
     * @return boolean
     */
    private function moveTaskWithinSameColumn(array $task, $position)
    {
        $results = array();
        $max = $this->getQuery($task['project_id'], $task['swimlane_id'], $task['column_id'])->count();
        $position = $max > 0 && $position > $max ? $max : $position;

        if ($position >= $max) {
            $results[] = $this->getQuery($task['project_id'], $task['swimlane_id'], $task['column_id'])->lte('position', $position)->decrement('position', 1);
        } else {
            $results[] = $this->getQuery($task['project_id'], $task['swimlane_id'], $task['column_id'])->gte('position', $position)->increment('position', 1);
        }

        $results[] = $this->updateTaskPosition($task['id'], $task['swimlane_id'], $task['column_id'], $position);

        return !in_array(false, $results, true);
    }

    /**
     * Update final task position
     *
     * @access private
     * @param  integer $task_id
     * @param  integer $swimlane_id
     * @param  integer $column_id
     * @param  integer $position
     * @return boolean
     */
    private function updateTaskPosition($task_id, $swimlane_id, $column_id, $position)
    {
        $now = time();

        return $this->db->table(Task::TABLE)
            ->eq('id', $task_id)
            ->eq('is_active', 1)
            ->update(array(
                'position' => $position,
                'column_id' => $column_id,
                'swimlane_id' => $swimlane_id,
                'date_modification' => $now,
                'date_moved' => $now,
            ));
    }

    /**
     * Get common query
     *
     * @access private
     * @param  integer $project_id
     * @param  integer $swimlane_id
     * @param  integer $column_id
     * @return \PicoDb\Table
     */
    private function getQuery($project_id, $swimlane_id, $column_id)
    {
        return $this->db->table(Task::TABLE)
            ->eq('project_id', $project_id)
            ->eq('swimlane_id', $swimlane_id)
            ->eq('column_id', $column_id)
            ->eq('is_active', 1);
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
            $this->logger->debug('Event fired: '.Task::EVENT_MOVE_SWIMLANE);
            $this->dispatcher->dispatch(Task::EVENT_MOVE_SWIMLANE, new TaskEvent($event_data));
        } elseif ($task['column_id'] != $new_column_id) {
            $this->logger->debug('Event fired: '.Task::EVENT_MOVE_COLUMN);
            $this->dispatcher->dispatch(Task::EVENT_MOVE_COLUMN, new TaskEvent($event_data));
        } elseif ($task['position'] != $new_position) {
            $this->logger->debug('Event fired: '.Task::EVENT_MOVE_POSITION);
            $this->dispatcher->dispatch(Task::EVENT_MOVE_POSITION, new TaskEvent($event_data));
        }
    }
}
