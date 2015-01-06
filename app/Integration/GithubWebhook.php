<?php

namespace Integration;

use Event\GenericEvent;
use Model\Task;

/**
 * Github Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class GithubWebhook extends Base
{
    /**
     * Events
     *
     * @var string
     */
    const EVENT_ISSUE_OPENED           = 'github.webhook.issue.opened';
    const EVENT_ISSUE_CLOSED           = 'github.webhook.issue.closed';
    const EVENT_ISSUE_REOPENED         = 'github.webhook.issue.reopened';
    const EVENT_ISSUE_ASSIGNEE_CHANGE  = 'github.webhook.issue.assignee';
    const EVENT_ISSUE_LABEL_CHANGE     = 'github.webhook.issue.label';
    const EVENT_ISSUE_COMMENT          = 'github.webhook.issue.commented';
    const EVENT_COMMIT                 = 'github.webhook.commit';

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
     * Parse Github events
     *
     * @access public
     * @param  string  $type      Github event type
     * @param  array   $payload   Github event
     * @return boolean
     */
    public function parsePayload($type, array $payload)
    {
        switch ($type) {
            case 'push':
                return $this->parsePushEvent($payload);
            case 'issues':
                return $this->parseIssueEvent($payload);
            case 'issue_comment':
                return $this->parseCommentIssueEvent($payload);
        }

        return false;
    }

    /**
     * Parse Push events (list of commits)
     *
     * @access public
     * @param  array   $payload   Event data
     * @return boolean
     */
    public function parsePushEvent(array $payload)
    {
        foreach ($payload['commits'] as $commit) {

            $task_id = $this->task->getTaskIdFromText($commit['message']);

            if (! $task_id) {
                continue;
            }

            $task = $this->taskFinder->getById($task_id);

            if (! $task) {
                continue;
            }

            if ($task['is_active'] == Task::STATUS_OPEN && $task['project_id'] == $this->project_id) {
                $this->container['dispatcher']->dispatch(
                    self::EVENT_COMMIT,
                    new GenericEvent(array('task_id' => $task_id) + $task)
                );
            }
        }

        return true;
    }

    /**
     * Parse issue events
     *
     * @access public
     * @param  array   $payload   Event data
     * @return boolean
     */
    public function parseIssueEvent(array $payload)
    {
        switch ($payload['action']) {
            case 'opened':
                return $this->handleIssueOpened($payload['issue']);
            case 'closed':
                return $this->handleIssueClosed($payload['issue']);
            case 'reopened':
                return $this->handleIssueReopened($payload['issue']);
            case 'assigned':
                return $this->handleIssueAssigned($payload['issue']);
            case 'unassigned':
                return $this->handleIssueUnassigned($payload['issue']);
            case 'labeled':
                return $this->handleIssueLabeled($payload['issue'], $payload['label']);
            case 'unlabeled':
                return $this->handleIssueUnlabeled($payload['issue'], $payload['label']);
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
    public function parseCommentIssueEvent(array $payload)
    {
        $task = $this->taskFinder->getByReference($payload['issue']['number']);
        $user = $this->user->getByUsername($payload['comment']['user']['login']);

        if ($task && $user) {

            $event = array(
                'project_id' => $this->project_id,
                'reference' => $payload['comment']['id'],
                'comment' => $payload['comment']['body'],
                'user_id' => $user['id'],
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
     * @param  array    $issue   Issue data
     * @return boolean
     */
    public function handleIssueOpened(array $issue)
    {
        $event = array(
            'project_id' => $this->project_id,
            'reference' => $issue['number'],
            'title' => $issue['title'],
            'description' => $issue['body']."\n\n[".t('Github Issue').']('.$issue['html_url'].')',
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
        $task = $this->taskFinder->getByReference($issue['number']);

        if ($task) {
            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['number'],
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
     * Handle issue reopened
     *
     * @access public
     * @param  array    $issue   Issue data
     * @return boolean
     */
    public function handleIssueReopened(array $issue)
    {
        $task = $this->taskFinder->getByReference($issue['number']);

        if ($task) {
            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['number'],
            );

            $this->container['dispatcher']->dispatch(
                self::EVENT_ISSUE_REOPENED,
                new GenericEvent($event)
            );

            return true;
        }

        return false;
    }

    /**
     * Handle issue assignee change
     *
     * @access public
     * @param  array    $issue   Issue data
     * @return boolean
     */
    public function handleIssueAssigned(array $issue)
    {
        $user = $this->user->getByUsername($issue['assignee']['login']);
        $task = $this->taskFinder->getByReference($issue['number']);

        if ($user && $task) {

            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'owner_id' => $user['id'],
                'reference' => $issue['number'],
            );

            $this->container['dispatcher']->dispatch(
                self::EVENT_ISSUE_ASSIGNEE_CHANGE,
                new GenericEvent($event)
            );

            return true;
        }

        return false;
    }

    /**
     * Handle unassigned issue
     *
     * @access public
     * @param  array    $issue   Issue data
     * @return boolean
     */
    public function handleIssueUnassigned(array $issue)
    {
        $task = $this->taskFinder->getByReference($issue['number']);

        if ($task) {

            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'owner_id' => 0,
                'reference' => $issue['number'],
            );

            $this->container['dispatcher']->dispatch(
                self::EVENT_ISSUE_ASSIGNEE_CHANGE,
                new GenericEvent($event)
            );

            return true;
        }

        return false;
    }

    /**
     * Handle labeled issue
     *
     * @access public
     * @param  array    $issue   Issue data
     * @param  array    $label   Label data
     * @return boolean
     */
    public function handleIssueLabeled(array $issue, array $label)
    {
        $task = $this->taskFinder->getByReference($issue['number']);

        if ($task) {

            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['number'],
                'label' => $label['name'],
            );

            $this->container['dispatcher']->dispatch(
                self::EVENT_ISSUE_LABEL_CHANGE,
                new GenericEvent($event)
            );

            return true;
        }

        return false;
    }

    /**
     * Handle unlabeled issue
     *
     * @access public
     * @param  array    $issue   Issue data
     * @param  array    $label   Label data
     * @return boolean
     */
    public function handleIssueUnlabeled(array $issue, array $label)
    {
        $task = $this->taskFinder->getByReference($issue['number']);

        if ($task) {

            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['number'],
                'label' => $label['name'],
                'category_id' => 0,
            );

            $this->container['dispatcher']->dispatch(
                self::EVENT_ISSUE_LABEL_CHANGE,
                new GenericEvent($event)
            );

            return true;
        }

        return false;
    }
}
