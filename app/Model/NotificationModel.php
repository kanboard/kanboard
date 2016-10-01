<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\EventBuilder\CommentEventBuilder;
use Kanboard\EventBuilder\EventIteratorBuilder;
use Kanboard\EventBuilder\SubtaskEventBuilder;
use Kanboard\EventBuilder\TaskEventBuilder;
use Kanboard\EventBuilder\TaskFileEventBuilder;
use Kanboard\EventBuilder\TaskLinkEventBuilder;

/**
 * Notification Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class NotificationModel extends Base
{
    /**
     * Get the event title with author
     *
     * @access public
     * @param  string $eventAuthor
     * @param  string $eventName
     * @param  array  $eventData
     * @return string
     */
    public function getTitleWithAuthor($eventAuthor, $eventName, array $eventData)
    {
        foreach ($this->getIteratorBuilder() as $builder) {
            $title = $builder->buildTitleWithAuthor($eventAuthor, $eventName, $eventData);

            if ($title !== '') {
                return $title;
            }
        }

        return e('Notification');
    }

    /**
     * Get the event title without author
     *
     * @access public
     * @param  string $eventName
     * @param  array  $eventData
     * @return string
     */
    public function getTitleWithoutAuthor($eventName, array $eventData)
    {
        foreach ($this->getIteratorBuilder() as $builder) {
            $title = $builder->buildTitleWithoutAuthor($eventName, $eventData);

            if ($title !== '') {
                return $title;
            }
        }

        return e('Notification');
    }

    /**
     * Get task id from event
     *
     * @access public
     * @param  string $eventName
     * @param  array  $eventData
     * @return integer
     */
    public function getTaskIdFromEvent($eventName, array $eventData)
    {
        if ($eventName === TaskModel::EVENT_OVERDUE) {
            return $eventData['tasks'][0]['id'];
        }

        return isset($eventData['task']['id']) ? $eventData['task']['id'] : 0;
    }

    /**
     * Get iterator builder
     *
     * @access protected
     * @return EventIteratorBuilder
     */
    protected function getIteratorBuilder()
    {
        $iterator = new EventIteratorBuilder();
        $iterator
            ->withBuilder(TaskEventBuilder::getInstance($this->container))
            ->withBuilder(CommentEventBuilder::getInstance($this->container))
            ->withBuilder(SubtaskEventBuilder::getInstance($this->container))
            ->withBuilder(TaskFileEventBuilder::getInstance($this->container))
            ->withBuilder(TaskLinkEventBuilder::getInstance($this->container))
        ;

        return $iterator;
    }
}
