<?php

namespace Kanboard\Integration;

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;

/**
 * Gitlab Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class GitlabWebhook extends \Kanboard\Core\Base
{
    /**
     * Events
     *
     * @var string
     */
    const EVENT_ISSUE_OPENED           = 'gitlab.webhook.issue.opened';
    const EVENT_ISSUE_CLOSED           = 'gitlab.webhook.issue.closed';
    const EVENT_COMMIT                 = 'gitlab.webhook.commit';
    const EVENT_ISSUE_COMMENT          = 'gitlab.webhook.issue.commented';

    /**
     * Supported webhook events
     *
     * @var string
     */
    const TYPE_PUSH    = 'push';
    const TYPE_ISSUE   = 'issue';
    const TYPE_COMMENT = 'comment';

    /**
     * Project id
     *
     * @access private
     * @var integer
     */
    private $project_id = 0;

    /**
     * Set the project id
     *
     * @access public
     * @param  integer   $project_id   Project id
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;
    }

    /**
     * Parse events
     *
     * @access public
     * @param  array   $payload   Gitlab event
     * @return boolean
     */
    public function parsePayload(array $payload)
    {
        switch ($this->getType($payload)) {
            case self::TYPE_PUSH:
                return $this->handlePushEvent($payload);
            case self::TYPE_ISSUE;
                return $this->handleIssueEvent($payload);
            case self::TYPE_COMMENT;
                return $this->handleCommentEvent($payload);
        }

        return false;
    }

    /**
     * Get event type
     *
     * @access public
     * @param  array   $payload   Gitlab event
     * @return string
     */
    public function getType(array $payload)
    {
        if (empty($payload['object_kind'])) {
            return '';
        }

        switch ($payload['object_kind']) {
            case 'issue':
                return self::TYPE_ISSUE;
            case 'note':
                return self::TYPE_COMMENT;
            case 'push':
                return self::TYPE_PUSH;
            default:
                return '';
        }
    }

    /**
     * Parse push event
     *
     * @access public
     * @param  array   $payload   Gitlab event
     * @return boolean
     */
    public function handlePushEvent(array $payload)
    {
        foreach ($payload['commits'] as $commit) {
            $this->handleCommit($commit);
        }

        return true;
    }

    /**
     * Parse commit
     *
     * @access public
     * @param  array   $commit   Gitlab commit
     * @return boolean
     */
    public function handleCommit(array $commit)
    {
        $task_id = $this->task->getTaskIdFromText($commit['message']);

        if (empty($task_id)) {
            return false;
        }

        $task = $this->taskFinder->getById($task_id);

        if (empty($task)) {
            return false;
        }

        if ($task['project_id'] != $this->project_id) {
            return false;
        }

        $this->container['dispatcher']->dispatch(
            self::EVENT_COMMIT,
            new GenericEvent(array(
                'task_id' => $task_id,
                'commit_message' => $commit['message'],
                'commit_url' => $commit['url'],
                'commit_comment' => $commit['message']."\n\n[".t('Commit made by @%s on Gitlab', $commit['author']['name']).']('.$commit['url'].')'
            ) + $task)
        );

        return true;
    }

    /**
     * Parse issue event
     *
     * @access public
     * @param  array   $payload   Gitlab event
     * @return boolean
     */
    public function handleIssueEvent(array $payload)
    {
        switch ($payload['object_attributes']['action']) {
            case 'open':
                return $this->handleIssueOpened($payload['object_attributes']);
            case 'close':
                return $this->handleIssueClosed($payload['object_attributes']);
        }

        return false;
    }

    /**
     * Handle new issues
     *
     * @access public
     * @param  array    $issue   Issue data
     * @return boolean
     */
    public function handleIssueOpened(array $issue)
    {
        $event = array(
            'project_id' => $this->project_id,
            'reference' => $issue['id'],
            'title' => $issue['title'],
            'description' => $issue['description']."\n\n[".t('Gitlab Issue').']('.$issue['url'].')',
        );

        $this->container['dispatcher']->dispatch(
            self::EVENT_ISSUE_OPENED,
            new GenericEvent($event)
        );

        return true;
    }

    /**
     * Handle issue closing
     *
     * @access public
     * @param  array    $issue   Issue data
     * @return boolean
     */
    public function handleIssueClosed(array $issue)
    {
        $task = $this->taskFinder->getByReference($this->project_id, $issue['id']);

        if (! empty($task)) {
            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['id'],
            );

            $this->container['dispatcher']->dispatch(
                self::EVENT_ISSUE_CLOSED,
                new GenericEvent($event)
            );

            return true;
        }

        return false;
    }

    /**
     * Parse comment issue events
     *
     * @access public
     * @param  array   $payload   Event data
     * @return boolean
     */
    public function handleCommentEvent(array $payload)
    {
        if (! isset($payload['issue'])) {
            return false;
        }

        $task = $this->taskFinder->getByReference($this->project_id, $payload['issue']['id']);

        if (! empty($task)) {
            $user = $this->user->getByUsername($payload['user']['username']);

            if (! empty($user) && ! $this->projectPermission->isMember($this->project_id, $user['id'])) {
                $user = array();
            }

            $event = array(
                'project_id' => $this->project_id,
                'reference' => $payload['object_attributes']['id'],
                'comment' => $payload['object_attributes']['note']."\n\n[".t('By @%s on Gitlab', $payload['user']['username']).']('.$payload['object_attributes']['url'].')',
                'user_id' => ! empty($user) ? $user['id'] : 0,
                'task_id' => $task['id'],
            );

            $this->container['dispatcher']->dispatch(
                self::EVENT_ISSUE_COMMENT,
                new GenericEvent($event)
            );

            return true;
        }

        return false;
    }
}
