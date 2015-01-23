<?php

namespace Integration;

use Event\GenericEvent;
use Event\TaskEvent;
use Model\Task;

/**
 * Gitlab Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class GitlabWebhook extends Base
{
    /**
     * Events
     *
     * @var string
     */
    const EVENT_ISSUE_OPENED           = 'gitlab.webhook.issue.opened';
    const EVENT_ISSUE_CLOSED           = 'gitlab.webhook.issue.closed';
    const EVENT_COMMIT                 = 'gitlab.webhook.commit';

    /**
     * Supported webhook events
     *
     * @var string
     */
    const TYPE_PUSH  = 'push';
    const TYPE_ISSUE = 'issue';

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
        if (isset($payload['object_kind']) && $payload['object_kind'] === 'issue') {
            return self::TYPE_ISSUE;
        }

        if (isset($payload['commits'])) {
            return self::TYPE_PUSH;
        }

        return '';
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

        if (! $task_id) {
            return false;
        }

        $task = $this->taskFinder->getById($task_id);

        if (! $task) {
            return false;
        }

        if ($task['is_active'] == Task::STATUS_OPEN && $task['project_id'] == $this->project_id) {

            $this->container['dispatcher']->dispatch(
                self::EVENT_COMMIT,
                new TaskEvent(array('task_id' => $task_id) + $task)
            );

            return true;
        }

        return false;
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
        $task = $this->taskFinder->getByReference($issue['id']);

        if ($task) {
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
}
