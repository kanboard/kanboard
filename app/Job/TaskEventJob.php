<?php

namespace Kanboard\Job;

use Kanboard\Event\TaskEvent;
use Kanboard\EventBuilder\TaskEventBuilder;
use Kanboard\Model\TaskModel;

/**
 * Class TaskEventJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class TaskEventJob extends BaseJob
{
    /**
     * Set job params
     *
     * @param  int    $taskId
     * @param  array  $eventNames
     * @param  array  $changes
     * @param  array  $values
     * @param  array  $task
     * @return $this
     */
    public function withParams($taskId, array $eventNames, array $changes = array(), array $values = array(), array $task = array())
    {
        $this->jobParams = array($taskId, $eventNames, $changes, $values, $task);
        return $this;
    }

    /**
     * Execute job
     *
     * @param  int    $taskId
     * @param  array  $eventNames
     * @param  array  $changes
     * @param  array  $values
     * @param  array  $task
     */
    public function execute($taskId, array $eventNames, array $changes = array(), array $values = array(), array $task = array())
    {
        $event = TaskEventBuilder::getInstance($this->container)
            ->withTaskId($taskId)
            ->withChanges($changes)
            ->withValues($values)
            ->withTask($task)
            ->buildEvent();

        if ($event !== null) {
            foreach ($eventNames as $eventName) {
                $this->fireEvent($eventName, $event);
            }
        }
    }

    /**
     * Trigger event
     *
     * @access protected
     * @param  string    $eventName
     * @param  TaskEvent $event
     */
    protected function fireEvent($eventName, TaskEvent $event)
    {
        $this->logger->debug(__METHOD__.' Event fired: '.$eventName);
        $this->dispatcher->dispatch($eventName, $event);

        if ($eventName === TaskModel::EVENT_CREATE) {
            $userMentionJob = $this->userMentionJob->withParams($event['task']['description'], TaskModel::EVENT_USER_MENTION, $event);
            $this->queueManager->push($userMentionJob);
        }
    }
}
