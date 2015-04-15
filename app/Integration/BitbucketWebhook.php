<?php

namespace Integration;

use Event\TaskEvent;
use Model\Task;

/**
 * Bitbucket Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class BitbucketWebhook extends Base
{
    /**
     * Events
     *
     * @var string
     */
    const EVENT_COMMIT = 'bitbucket.webhook.commit';

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
        if (! empty($payload['commits'])) {

            foreach ($payload['commits'] as $commit) {

                if ($this->handleCommit($commit)) {
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

        if (empty($task)) {
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
}
