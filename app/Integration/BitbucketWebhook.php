<?php

namespace Kanboard\Integration;

use Kanboard\Event\GenericEvent;
use Kanboard\Model\Task;

/**
 * Bitbucket Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class BitbucketWebhook extends \Kanboard\Core\Base
{
    /**
     * Events
     *
     * @var string
     */
    const EVENT_COMMIT                 = 'bitbucket.webhook.commit';
    const EVENT_ISSUE_OPENED           = 'bitbucket.webhook.issue.opened';
    const EVENT_ISSUE_CLOSED           = 'bitbucket.webhook.issue.closed';
    const EVENT_ISSUE_REOPENED         = 'bitbucket.webhook.issue.reopened';
    const EVENT_ISSUE_ASSIGNEE_CHANGE  = 'bitbucket.webhook.issue.assignee';
    const EVENT_ISSUE_COMMENT          = 'bitbucket.webhook.issue.commented';

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
     * Parse incoming events
     *
     * @access public
     * @param  string  $type      Bitbucket event type
     * @param  array   $payload   Bitbucket event
     * @return boolean
     */
    public function parsePayload($type, array $payload)
    {
        switch ($type) {
            case 'issue:comment_created':
                return $this->handleCommentCreated($payload);
            case 'issue:created':
                return $this->handleIssueOpened($payload);
            case 'issue:updated':
                return $this->handleIssueUpdated($payload);
            case 'repo:push':
                return $this->handlePush($payload);
        }

        return false;
    }

    /**
     * Parse comment issue events
     *
     * @access public
     * @param  array   $payload
     * @return boolean
     */
    public function handleCommentCreated(array $payload)
    {
        $task = $this->taskFinder->getByReference($this->project_id, $payload['issue']['id']);

        if (! empty($task)) {
            $user = $this->user->getByUsername($payload['actor']['username']);

            if (! empty($user) && ! $this->projectPermission->isMember($this->project_id, $user['id'])) {
                $user = array();
            }

            $event = array(
                'project_id' => $this->project_id,
                'reference' => $payload['comment']['id'],
                'comment' => $payload['comment']['content']['raw']."\n\n[".t('By @%s on Bitbucket', $payload['actor']['display_name']).']('.$payload['comment']['links']['html']['href'].')',
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

    /**
     * Handle new issues
     *
     * @access public
     * @param  array    $payload
     * @return boolean
     */
    public function handleIssueOpened(array $payload)
    {
        $event = array(
            'project_id' => $this->project_id,
            'reference' => $payload['issue']['id'],
            'title' => $payload['issue']['title'],
            'description' => $payload['issue']['content']['raw']."\n\n[".t('Bitbucket Issue').']('.$payload['issue']['links']['html']['href'].')',
        );

        $this->container['dispatcher']->dispatch(
            self::EVENT_ISSUE_OPENED,
            new GenericEvent($event)
        );

        return true;
    }

    /**
     * Handle issue updates
     *
     * @access public
     * @param  array    $payload
     * @return boolean
     */
    public function handleIssueUpdated(array $payload)
    {
        $task = $this->taskFinder->getByReference($this->project_id, $payload['issue']['id']);

        if (empty($task)) {
            return false;
        }

        if (isset($payload['changes']['status'])) {
            return $this->handleStatusChange($task, $payload);
        } elseif (isset($payload['changes']['assignee'])) {
            return $this->handleAssigneeChange($task, $payload);
        }

        return false;
    }

    /**
     * Handle issue status change
     *
     * @access public
     * @param  array    $task
     * @param  array    $payload
     * @return boolean
     */
    public function handleStatusChange(array $task, array $payload)
    {
        $event = array(
            'project_id' => $this->project_id,
            'task_id' => $task['id'],
            'reference' => $payload['issue']['id'],
        );

        switch ($payload['issue']['state']) {
            case 'closed':
                $this->container['dispatcher']->dispatch(self::EVENT_ISSUE_CLOSED, new GenericEvent($event));
                return true;
            case 'open':
                $this->container['dispatcher']->dispatch(self::EVENT_ISSUE_REOPENED, new GenericEvent($event));
                return true;
        }

        return false;
    }

    /**
     * Handle issue assignee change
     *
     * @access public
     * @param  array    $task
     * @param  array    $payload
     * @return boolean
     */
    public function handleAssigneeChange(array $task, array $payload)
    {
        if (empty($payload['issue']['assignee'])) {
            return $this->handleIssueUnassigned($task, $payload);
        }

        return $this->handleIssueAssigned($task, $payload);
    }

    /**
     * Handle issue assigned
     *
     * @access public
     * @param  array    $task
     * @param  array    $payload
     * @return boolean
     */
    public function handleIssueAssigned(array $task, array $payload)
    {
        $user = $this->user->getByUsername($payload['issue']['assignee']['username']);

        if (empty($user)) {
            return false;
        }

        if (! $this->projectPermission->isMember($this->project_id, $user['id'])) {
            return false;
        }

        $event = array(
            'project_id' => $this->project_id,
            'task_id' => $task['id'],
            'owner_id' => $user['id'],
            'reference' => $payload['issue']['id'],
        );

        $this->container['dispatcher']->dispatch(self::EVENT_ISSUE_ASSIGNEE_CHANGE, new GenericEvent($event));

        return true;
    }

    /**
     * Handle issue unassigned
     *
     * @access public
     * @param  array    $task
     * @param  array    $payload
     * @return boolean
     */
    public function handleIssueUnassigned(array $task, array $payload)
    {
        $event = array(
            'project_id' => $this->project_id,
            'task_id' => $task['id'],
            'owner_id' => 0,
            'reference' => $payload['issue']['id'],
        );

        $this->container['dispatcher']->dispatch(self::EVENT_ISSUE_ASSIGNEE_CHANGE, new GenericEvent($event));

        return true;
    }

    /**
     * Parse push events
     *
     * @access public
     * @param  array   $payload
     * @return boolean
     */
    public function handlePush(array $payload)
    {
        if (isset($payload['push']['changes'])) {
            foreach ($payload['push']['changes'] as $change) {
                if (isset($change['new']['target']) && $this->handleCommit($change['new']['target'], $payload['actor'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Parse commit
     *
     * @access public
     * @param  array   $commit   Bitbucket commit
     * @param  array   $actor    Bitbucket actor
     * @return boolean
     */
    public function handleCommit(array $commit, array $actor)
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
                'commit_url' => $commit['links']['html']['href'],
                'commit_comment' => $commit['message']."\n\n[".t('Commit made by @%s on Bitbucket', $actor['display_name']).']('.$commit['links']['html']['href'].')',
            ) + $task)
        );

        return true;
    }
}
