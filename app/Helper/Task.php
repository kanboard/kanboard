<?php

namespace Kanboard\Helper;

/**
 * Task helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Task extends \Kanboard\Core\Base
{
    public function getColors()
    {
        return $this->color->getList();
    }

    public function recurrenceTriggers()
    {
        return $this->task->getRecurrenceTriggerList();
    }

    public function recurrenceTimeframes()
    {
        return $this->task->getRecurrenceTimeframeList();
    }

    public function recurrenceBasedates()
    {
        return $this->task->getRecurrenceBasedateList();
    }

    public function canRemove(array $task)
    {
        return $this->taskPermission->canRemoveTask($task);
    }
}
