<?php

namespace Action;

use Integration\GitlabWebhook;
use Integration\GithubWebhook;
use Model\Task;

/**
 * Close automatically a task
 *
 * @package action
 * @author  Frederic Guillot
 */
class TaskClose extends Base
{
    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array(
            Task::EVENT_MOVE_COLUMN,
            GithubWebhook::EVENT_COMMIT,
            GithubWebhook::EVENT_ISSUE_CLOSED,
            GitlabWebhook::EVENT_COMMIT,
            GitlabWebhook::EVENT_ISSUE_CLOSED,
        );
    }

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return array
     */
    public function getActionRequiredParameters()
    {
        switch ($this->event_name) {
            case GithubWebhook::EVENT_COMMIT:
            case GithubWebhook::EVENT_ISSUE_CLOSED:
            case GitlabWebhook::EVENT_COMMIT:
            case GitlabWebhook::EVENT_ISSUE_CLOSED:
                return array();
            default:
                return array('column_id' => t('Column'));
        }
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        switch ($this->event_name) {
            case GithubWebhook::EVENT_COMMIT:
            case GithubWebhook::EVENT_ISSUE_CLOSED:
            case GitlabWebhook::EVENT_COMMIT:
            case GitlabWebhook::EVENT_ISSUE_CLOSED:
                return array('task_id');
            default:
                return array('task_id', 'column_id');
        }
    }

    /**
     * Execute the action (close the task)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        return $this->taskStatus->close($data['task_id']);
    }

    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
        switch ($this->event_name) {
            case GithubWebhook::EVENT_COMMIT:
            case GithubWebhook::EVENT_ISSUE_CLOSED:
            case GitlabWebhook::EVENT_COMMIT:
            case GitlabWebhook::EVENT_ISSUE_CLOSED:
                return true;
            default:
                return $data['column_id'] == $this->getParam('column_id');
        }
    }
}
