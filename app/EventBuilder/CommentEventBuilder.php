<?php

namespace Kanboard\EventBuilder;

use Kanboard\Event\CommentEvent;

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
    public function build()
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
}
