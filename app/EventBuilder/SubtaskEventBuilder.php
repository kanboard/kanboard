<?php

namespace Kanboard\EventBuilder;

use Kanboard\Event\SubtaskEvent;
use Kanboard\Event\GenericEvent;

/**
 * Class SubtaskEventBuilder
 *
 * @package Kanboard\EventBuilder
 * @author  Frederic Guillot
 */
class SubtaskEventBuilder extends BaseEventBuilder
{
    /**
     * SubtaskId
     *
     * @access protected
     * @var int
     */
    protected $subtaskId = 0;

    /**
     * Changed values
     *
     * @access protected
     * @var array
     */
    protected $values = array();

    /**
     * Set SubtaskId
     *
     * @param  int $subtaskId
     * @return $this
     */
    public function withSubtaskId($subtaskId)
    {
        $this->subtaskId = $subtaskId;
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
     * Build event data
     *
     * @access public
     * @return GenericEvent|null
     */
    public function build()
    {
        $eventData = array();
        $eventData['subtask'] = $this->subtaskModel->getById($this->subtaskId, true);

        if (empty($eventData['subtask'])) {
            $this->logger->debug(__METHOD__.': Subtask not found');
            return null;
        }

        if (! empty($this->values)) {
            $eventData['changes'] = array_diff_assoc($this->values, $eventData['subtask']);
        }

        $eventData['task'] = $this->taskFinderModel->getDetails($eventData['subtask']['task_id']);
        return new SubtaskEvent($eventData);
    }
}
