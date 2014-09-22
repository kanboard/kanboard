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
    const EVENT_ISSUE_OPENED  = 'github.webhook.issue.opened';
    const EVENT_ISSUE_CLOSED  = 'github.webhook.issue.closed';
    const EVENT_ISSUE_LABELED = 'github.webhook.issue.labeled';
    const EVENT_ISSUE_COMMENT = 'github.webhook.issue.commented';
    const EVENT_COMMIT        = 'github.webhook.commit';

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

    }
}
