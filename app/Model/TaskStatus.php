<?php

namespace Model;

use Event\TaskEvent;

/**
 * Task Status
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskStatus extends Base
{
    /**
     * Return the list of statuses
     *
     * @access public
     * @param  boolean   $prepend  Prepend default value
     * @return array
     */
    public function getList($prepend = false)
    {
        $listing = $prepend ? array(-1 => t('All status')) : array();

        return $listing + array(
            Task::STATUS_OPEN => t('Open'),
            Task::STATUS_CLOSED => t('Closed'),
        );
    }

    /**
     * Return true if the task is closed
     *
     * @access public
     * @param  integer    $task_id     Task id
     * @return boolean
     */
    public function isClosed($task_id)
    {
        return $this->checkStatus($task_id, Task::STATUS_CLOSED);
    }

    /**
     * Return true if the task is open
     *
     * @access public
     * @param  integer    $task_id     Task id
     * @return boolean
     */
    public function isOpen($task_id)
    {
        return $this->checkStatus($task_id, Task::STATUS_OPEN);
    }

    /**
     * Mark a task closed
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function close($task_id)
    {
        return $this->changeStatus($task_id, Task::STATUS_CLOSED, time(), Task::EVENT_CLOSE);
    }

    /**
     * Mark a task open
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return boolean
     */
    public function open($task_id)
    {
        return $this->changeStatus($task_id, Task::STATUS_OPEN, 0, Task::EVENT_OPEN);
    }

    /**
     * Common method to change the status of task
     *
     * @access private
     * @param  integer   $task_id             Task id
     * @param  integer   $status              Task status
     * @param  integer   $date_completed      Timestamp
     * @param  string    $event               Event name
     * @return boolean
     */
    private function changeStatus($task_id, $status, $date_completed, $event)
    {
        if (! $this->taskFinder->exists($task_id)) {
            return false;
        }

        $result = $this->db
                        ->table(Task::TABLE)
                        ->eq('id', $task_id)
                        ->update(array(
                            'is_active' => $status,
                            'date_completed' => $date_completed,
                            'date_modification' => time(),
                        ));

        if ($result) {
            $this->container['dispatcher']->dispatch(
                $event,
                new TaskEvent(array('task_id' => $task_id) + $this->taskFinder->getById($task_id))
            );
        }

        return $result;
    }

    /**
     * Check the status of task
     *
     * @access private
     * @param  integer   $task_id   Task id
     * @param  integer   $status    Task status
     * @return boolean
     */
    private function checkStatus($task_id, $status)
    {
        return $this->db
                    ->table(Task::TABLE)
                    ->eq('id', $task_id)
                    ->eq('is_active', $status)
                    ->count() === 1;
    }
}
