<?php

namespace Kanboard\Job;

use Kanboard\EventBuilder\CommentEventBuilder;
use Kanboard\Model\CommentModel;

/**
 * Class CommentEventJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class CommentEventJob extends BaseJob
{
    /**
     * Set job params
     *
     * @param  int    $commentId
     * @param  string $eventName
     * @return $this
     */
    public function withParams($commentId, $eventName)
    {
        $this->jobParams = array($commentId, $eventName);
        return $this;
    }

    /**
     * Execute job
     *
     * @param  int    $commentId
     * @param  string $eventName
     */
    public function execute($commentId, $eventName)
    {
        $event = CommentEventBuilder::getInstance($this->container)
            ->withCommentId($commentId)
            ->buildEvent();

        if ($event !== null) {
            $this->dispatcher->dispatch($event, $eventName);

            if ($eventName === CommentModel::EVENT_CREATE) {
                $userMentionJob = $this->userMentionJob->withParams($event['comment']['comment'], CommentModel::EVENT_USER_MENTION, $event);
                $this->queueManager->push($userMentionJob);
            }
        }
    }
}
