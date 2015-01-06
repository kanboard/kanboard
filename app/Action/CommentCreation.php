<?php

namespace Action;

use Integration\GithubWebhook;

/**
 * Create automatically a comment from a webhook
 *
 * @package action
 * @author  Frederic Guillot
 */
class CommentCreation extends Base
{
    /**
     * Get the list of compatible events
     *
     * @access public
     * @return string[]
     */
    public function getCompatibleEvents()
    {
        return array(
            GithubWebhook::EVENT_ISSUE_COMMENT,
        );
    }

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return string[]
     */
    public function getActionRequiredParameters()
    {
        return array();
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return array
     */
    public function getEventRequiredParameters()
    {
        return array(
            'reference',
            'comment',
            'user_id',
            'task_id',
        );
    }

    /**
     * Execute the action (create a new comment)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        return (bool) $this->comment->create(array(
            'reference' => $data['reference'],
            'comment' => $data['comment'],
            'task_id' => $data['task_id'],
            'user_id' => $data['user_id'],
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
