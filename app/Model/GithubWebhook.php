<?php

namespace Model;

/**
 * Github Webhook model
 *
 * @package  model
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
     * @param  string  $payload   Raw Github event (JSON)
     */
    public function parsePayload($type, $payload)
    {
        $payload = json_decode($payload, true);

        switch ($type) {
            case 'push':
                return $this->parsePushEvent($payload);
            case 'issues':
                return $this->parseIssueEvent($payload);
        }
    }

    /**
     * Parse Push events (list of commits)
     *
     * @access public
     * @param  array   $payload   Event data
     */
    public function parsePushEvent(array $payload)
    {
        foreach ($payload['commits'] as $commit) {

            $task_id = $this->task->getTaskIdFromText($commit['message']);

            if (! $task_id) {
                continue;
            }

            $task = $this->task->getById($task_id);

            if (! $task) {
                continue;
            }

            if ($task['is_active'] == Task::STATUS_OPEN) {
                $this->event->trigger(self::EVENT_COMMIT, array('task_id' => $task_id) + $task);
            }
        }
    }

    /**
     * Parse issue events
     *
     * @access public
     * @param  array   $payload   Event data
     */
    public function parseIssueEvent(array $payload)
    {
        switch ($payload['action']) {
            case 'opened':
                $this->handleIssueOpened($payload['issue']);
                break;
            case 'closed':
                $this->handleIssueClosed($payload['issue']);
                break;
            case 'reopened':
                $this->handleIssueReopened($payload['issue']);
                break;
            case 'assigned':
                $this->handleIssueAssigned($payload['issue']);
                break;
            case 'unassigned':
                $this->handleIssueUnassigned($payload['issue']);
                break;
            case 'labeled':
                $this->handleIssueLabeled($payload['issue'], $payload['label']);
                break;
            case 'unlabeled':
                $this->handleIssueUnlabeled($payload['issue'], $payload['label']);
                break;
        }
    }

    /**
     * Handle new issues
     *
     * @access public
     * @param  array    $issue   Issue data
     */
    public function handleIssueOpened(array $issue)
    {
        $event = array(
            'project_id' => $this->project_id,
            'reference' => $issue['number'],
            'title' => $issue['title'],
            'description' => $issue['body']."\n\n[".t('Github Issue').']('.$issue['html_url'].')',
        );

        $this->event->trigger(self::EVENT_ISSUE_OPENED, $event);
    }

    /**
     * Handle issue closing
     *
     * @access public
     * @param  array    $issue   Issue data
     */
    public function handleIssueClosed(array $issue)
    {
        $task = $this->task->getByReference($issue['number']);

        if ($task) {
            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['number'],
            );

            $this->event->trigger(self::EVENT_ISSUE_CLOSED, $event);
        }
    }

    /**
     * Handle issue reopened
     *
     * @access public
     * @param  array    $issue   Issue data
     */
    public function handleIssueReopened(array $issue)
    {
        $task = $this->task->getByReference($issue['number']);

        if ($task) {
            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['number'],
            );

            $this->event->trigger(self::EVENT_ISSUE_REOPENED, $event);
        }
    }

    /**
     * Handle issue assignee change
     *
     * @access public
     * @param  array    $issue   Issue data
     */
    public function handleIssueAssigned(array $issue)
    {
        $user = $this->user->getByUsername($issue['assignee']['login']);
        $task = $this->task->getByReference($issue['number']);

        if ($user && $task) {

            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'owner_id' => $user['id'],
                'reference' => $issue['number'],
            );

            $this->event->trigger(self::EVENT_ISSUE_ASSIGNEE_CHANGE, $event);
        }
    }

    /**
     * Handle unassigned issue
     *
     * @access public
     * @param  array    $issue   Issue data
     */
    public function handleIssueUnassigned(array $issue)
    {
        $task = $this->task->getByReference($issue['number']);

        if ($task) {

            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'owner_id' => 0,
                'reference' => $issue['number'],
            );

            $this->event->trigger(self::EVENT_ISSUE_ASSIGNEE_CHANGE, $event);
        }
    }

    /**
     * Handle labeled issue
     *
     * @access public
     * @param  array    $issue   Issue data
     * @param  array    $label   Label data
     */
    public function handleIssueLabeled(array $issue, array $label)
    {
        $task = $this->task->getByReference($issue['number']);

        if ($task) {

            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['number'],
                'label' => $label['name'],
            );

            $this->event->trigger(self::EVENT_ISSUE_LABEL_CHANGE, $event);
        }
    }

    /**
     * Handle unlabeled issue
     *
     * @access public
     * @param  array    $issue   Issue data
     * @param  array    $label   Label data
     */
    public function handleIssueUnlabeled(array $issue, array $label)
    {
        $task = $this->task->getByReference($issue['number']);

        if ($task) {

            $event = array(
                'project_id' => $this->project_id,
                'task_id' => $task['id'],
                'reference' => $issue['number'],
                'label' => $label['name'],
                'category_id' => 0,
            );

            $this->event->trigger(self::EVENT_ISSUE_LABEL_CHANGE, $event);
        }
    }
}
