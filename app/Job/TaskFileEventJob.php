<?php

namespace Kanboard\Job;

use Kanboard\EventBuilder\TaskFileEventBuilder;

/**
 * Class TaskFileEventJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class TaskFileEventJob extends BaseJob
{
    /**
     * Set job params
     *
     * @param  int    $fileId
     * @param  string $eventName
     * @return $this
     */
    public function withParams($fileId, $eventName)
    {
        $this->jobParams = array($fileId, $eventName);
        return $this;
    }

    /**
     * Execute job
     *
     * @param  int    $fileId
     * @param  string $eventName
     */
    public function execute($fileId, $eventName)
    {
        $event = TaskFileEventBuilder::getInstance($this->container)
            ->withFileId($fileId)
            ->buildEvent();

        if ($event !== null) {
            $this->dispatcher->dispatch($eventName, $event);
        }
    }
}
