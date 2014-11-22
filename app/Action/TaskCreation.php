<?php

namespace Action;

use Model\GithubWebhook;

/**
 * Create automatically a task from a webhook
 *
 * @package action
 * @author  Frederic Guillot
 */
class TaskCreation extends Base
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
            GithubWebhook::EVENT_ISSUE_OPENED,
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
        return array();
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array(
            'reference',
            'title',
        );
    }

    /**
     * Execute the action (create a new task)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        return (bool) $this->taskCreation->create(array(
            'project_id' => $data['project_id'],
            'title' => $data['title'],
            'reference' => $data['reference'],
            'description' => $data['description'],
        ));
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
        return true;
    }
}
