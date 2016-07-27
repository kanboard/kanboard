<?php

namespace Kanboard\EventBuilder;

use Kanboard\Event\CommentEvent;
use Kanboard\Model\CommentModel;

/**
 * Class CommentEventBuilder
 *
 * @package Kanboard\EventBuilder
 * @author  Frederic Guillot
 */
class CommentEventBuilder extends BaseEventBuilder
{
    protected $commentId = 0;

    /**
     * Set commentId
     *
     * @param  int $commentId
     * @return $this
     */
    public function withCommentId($commentId)
    {
        $this->commentId = $commentId;
        return $this;
    }

    /**
     * Build event data
     *
     * @access public
     * @return CommentEvent|null
     */
    public function buildEvent()
    {
        $comment = $this->commentModel->getById($this->commentId);

        if (empty($comment)) {
            return null;
        }

        return new CommentEvent(array(
            'comment' => $comment,
            'task' => $this->taskFinderModel->getDetails($comment['task_id']),
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
        switch ($eventName) {
            case CommentModel::EVENT_UPDATE:
                return e('%s updated a comment on the task #%d', $author, $eventData['task']['id']);
            case CommentModel::EVENT_CREATE:
                return e('%s commented on the task #%d', $author, $eventData['task']['id']);
            case CommentModel::EVENT_DELETE:
                return e('%s removed a comment on the task #%d', $author, $eventData['task']['id']);
            case CommentModel::EVENT_USER_MENTION:
                return e('%s mentioned you in a comment on the task #%d', $author, $eventData['task']['id']);
            default:
                return '';
        }
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
        switch ($eventName) {
            case CommentModel::EVENT_CREATE:
                return e('New comment on task #%d', $eventData['comment']['task_id']);
            case CommentModel::EVENT_UPDATE:
                return e('Comment updated on task #%d', $eventData['comment']['task_id']);
            case CommentModel::EVENT_DELETE:
                return e('Comment removed on task #%d', $eventData['comment']['task_id']);
            case CommentModel::EVENT_USER_MENTION:
                return e('You were mentioned in a comment on the task #%d', $eventData['task']['id']);
            default:
                return '';
        }
    }
}
