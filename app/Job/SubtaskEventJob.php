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
     * @param  int   $subtaskId
     * @param  array $eventNames
     * @param  array $values
     * @return $this
     */
    public function withParams($subtaskId, array $eventNames, array $values = array())
    {
        $this->jobParams = array($subtaskId, $eventNames, $values);
        return $this;
    }

    /**
     * Execute job
     *
     * @param  int   $subtaskId
     * @param  array $eventNames
     * @param  array $values
     */
    public function execute($subtaskId, array $eventNames, array $values = array())
    {
        $event = SubtaskEventBuilder::getInstance($this->container)
            ->withSubtaskId($subtaskId)
            ->withValues($values)
            ->buildEvent();

        if ($event !== null) {
            foreach ($eventNames as $eventName) {
                $this->dispatcher->dispatch($eventName, $event);
            }
        }
    }
}
