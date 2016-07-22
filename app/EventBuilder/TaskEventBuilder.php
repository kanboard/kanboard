<?php

namespace Kanboard\EventBuilder;

use Kanboard\Event\TaskEvent;

/**
 * Class TaskEventBuilder
 *
 * @package Kanboard\EventBuilder
 * @author  Frederic Guillot
 */
class TaskEventBuilder extends BaseEventBuilder
{
    /**
     * TaskId
     *
     * @access protected
     * @var int
     */
    protected $taskId = 0;

    /**
     * Task
     *
     * @access protected
     * @var array
     */
    protected $task = array();

    /**
     * Extra values
     *
     * @access protected
     * @var array
     */
    protected $values = array();

    /**
     * Changed values
     *
     * @access protected
     * @var array
     */
    protected $changes = array();

    /**
     * Set TaskId
     *
     * @param  int $taskId
     * @return $this
     */
    public function withTaskId($taskId)
    {
        $this->taskId = $taskId;
        return $this;
    }

    /**
     * Set task
     *
     * @param  array $task
     * @return $this
     */
    public function withTask(array $task)
    {
        $this->task = $task;
        return $this;
    }

    /**
     * Set values
     *
     * @param  array $values
     * @return $this
     */
    public function withValues(array $values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Set changes
     *
     * @param  array $changes
     * @return $this
     */
    public function withChanges(array $changes)
    {
        $this->changes = $changes;
        return $this;
    }

    /**
     * Build event data
     *
     * @access public
     * @return TaskEvent|null
     */
    public function build()
    {
        $eventData = array();
        $eventData['task_id'] = $this->taskId;
        $eventData['task'] = $this->taskFinderModel->getDetails($this->taskId);

        if (empty($eventData['task'])) {
            $this->logger->debug(__METHOD__.': Task not found');
            return null;
        }

        if (! empty($this->changes)) {
            if (empty($this->task)) {
                $this->task = $eventData['task'];
            }

            $eventData['changes'] = array_diff_assoc($this->changes, $this->task);
            unset($eventData['changes']['date_modification']);
        }

        return new TaskEvent(array_merge($eventData, $this->values));
    }
}
