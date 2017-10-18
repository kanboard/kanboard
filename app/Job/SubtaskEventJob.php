<?php

namespace Kanboard\Job;

use Kanboard\EventBuilder\SubtaskEventBuilder;

/**
 * Class SubtaskEventJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class SubtaskEventJob extends BaseJob
{
    /**
     * Set job params
     *
     * @param  int     $subtaskId
     * @param  string  $eventName
     * @param  array   $values
     * @return $this
     */
    public function withParams($subtaskId, $eventName, array $values = array())
    {
        $this->jobParams = array($subtaskId, $eventName, $values);
        return $this;
    }

    /**
     * Execute job
     *
     * @param  int    $subtaskId
     * @param  string $eventName
     * @param  array  $values
     */
    public function execute($subtaskId, $eventName, array $values = array())
    {
        $event = SubtaskEventBuilder::getInstance($this->container)
            ->withSubtaskId($subtaskId)
            ->withValues($values)
            ->buildEvent();

        if ($event !== null) {
            $this->dispatcher->dispatch($eventName, $event);
        }
    }
}
