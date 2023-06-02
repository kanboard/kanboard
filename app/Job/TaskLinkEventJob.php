<?php

namespace Kanboard\Job;

use Kanboard\EventBuilder\TaskLinkEventBuilder;

/**
 * Class TaskLinkEventJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class TaskLinkEventJob extends BaseJob
{
    /**
     * Set job params
     *
     * @param  int    $taskLinkId
     * @param  string $eventName
     * @return $this
     */
    public function withParams($taskLinkId, $eventName)
    {
        $this->jobParams = array($taskLinkId, $eventName);
        return $this;
    }

    /**
     * Execute job
     *
     * @param  int    $taskLinkId
     * @param  string $eventName
     */
    public function execute($taskLinkId, $eventName)
    {
        $event = TaskLinkEventBuilder::getInstance($this->container)
            ->withTaskLinkId($taskLinkId)
            ->buildEvent();

        if ($event !== null) {
            $this->dispatcher->dispatch($event, $eventName);
        }
    }
}
