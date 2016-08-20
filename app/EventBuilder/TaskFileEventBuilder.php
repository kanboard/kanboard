<?php

namespace Kanboard\EventBuilder;

use Kanboard\Event\TaskFileEvent;
use Kanboard\Event\GenericEvent;
use Kanboard\Model\TaskFileModel;

/**
 * Class TaskFileEventBuilder
 *
 * @package Kanboard\EventBuilder
 * @author  Frederic Guillot
 */
class TaskFileEventBuilder extends BaseEventBuilder
{
    protected $fileId = 0;

    /**
     * Set fileId
     *
     * @param  int $fileId
     * @return $this
     */
    public function withFileId($fileId)
    {
        $this->fileId = $fileId;
        return $this;
    }

    /**
     * Build event data
     *
     * @access public
     * @return GenericEvent|null
     */
    public function buildEvent()
    {
        $file = $this->taskFileModel->getById($this->fileId);

        if (empty($file)) {
            $this->logger->debug(__METHOD__.': File not found');
            return null;
        }

        return new TaskFileEvent(array(
            'file' => $file,
            'task' => $this->taskFinderModel->getDetails($file['task_id']),
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
        if ($eventName === TaskFileModel::EVENT_CREATE) {
            return e('%s attached a file to the task #%d', $author, $eventData['task']['id']);
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
        if ($eventName === TaskFileModel::EVENT_CREATE) {
            return e('New attachment on task #%d: %s', $eventData['file']['task_id'], $eventData['file']['name']);
        }

        return '';
    }
}
