<?php

namespace Kanboard\EventBuilder;

use Kanboard\Event\TaskFileEvent;
use Kanboard\Event\GenericEvent;

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
    public function build()
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
}
