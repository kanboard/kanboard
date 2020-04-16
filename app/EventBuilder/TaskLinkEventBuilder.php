<?php

namespace Kanboard\EventBuilder;

use Kanboard\Event\TaskLinkEvent;
use Kanboard\Model\TaskLinkModel;

/**
 * Class TaskLinkEventBuilder
 *
 * @package Kanboard\EventBuilder
 * @author  Frederic Guillot
 */
class TaskLinkEventBuilder extends BaseEventBuilder
{
    protected $taskLinkId = 0;

    /**
     * Set taskLinkId
     *
     * @param  int $taskLinkId
     * @return $this
     */
    public function withTaskLinkId($taskLinkId)
    {
        $this->taskLinkId = $taskLinkId;
        return $this;
    }

    /**
     * Build event data
     *
     * @access public
     * @return TaskLinkEvent|null
     */
    public function buildEvent()
    {
        $taskLink = $this->taskLinkModel->getById($this->taskLinkId);

        if (empty($taskLink)) {
            $this->logger->debug(__METHOD__.': TaskLink not found');
            return null;
        }

        return new TaskLinkEvent(array(
            'task_link' => $taskLink,
            'task' => $this->taskFinderModel->getDetails($taskLink['task_id']),
        ));
    }

    /**
     * Get event title with author
     *
     * @access public
     * @param  string $author
     * @param  string $eventName
     * @param  array  $eventData
     * @return string
     */
    public function buildTitleWithAuthor($author, $eventName, array $eventData)
    {
        if ($eventName === TaskLinkModel::EVENT_CREATE_UPDATE) {
            return e('%s set a new internal link for the task #%d', $author, $eventData['task']['id']);
        } elseif ($eventName === TaskLinkModel::EVENT_DELETE) {
            return e('%s removed an internal link for the task #%d', $author, $eventData['task']['id']);
        }

        return '';
    }

    /**
     * Get event title without author
     *
     * @access public
     * @param  string $eventName
     * @param  array  $eventData
     * @return string
     */
    public function buildTitleWithoutAuthor($eventName, array $eventData)
    {
        if ($eventName === TaskLinkModel::EVENT_CREATE_UPDATE) {
            return e('A new internal link for the task #%d has been defined', $eventData['task']['id']);
        } elseif ($eventName === TaskLinkModel::EVENT_DELETE) {
            return e('Internal link removed for the task #%d', $eventData['task']['id']);
        }

        return '';
    }
}
