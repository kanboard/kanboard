<?php

namespace Helper;

/**
 * Task helpers
 *
 * @package helper
 * @author  Frederic Guillot
 */
class Task extends \Core\Base
{
    /**
     * Get the age of an item in quasi human readable format.
     * It's in this format: <1h , NNh, NNd
     *
     * @access public
     * @param  integer    $timestamp    Unix timestamp of the artifact for which age will be calculated
     * @param  integer    $now          Compare with this timestamp (Default value is the current unix timestamp)
     * @return string
     */
    public function age($timestamp, $now = null)
    {
        if ($now === null) {
            $now = time();
        }

        $diff = $now - $timestamp;

        if ($diff < 3600) {
            return t('<1h');
        }
        else if ($diff < 86400) {
            return t('%dh', $diff / 3600);
        }

        return t('%dd', ($now - $timestamp) / 86400);
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
