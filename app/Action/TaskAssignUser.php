<?php

namespace Kanboard\Action;

/**
 * Assign a task to someone
 *
 * @package Kanboard\Actionv
 * @author  Frederic Guillot
 */
class TaskAssignUser extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Change the assignee based on an external username');
    }

    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array();
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
            'task_id',
            'owner_id',
        );
    }

    /**
     * Execute the action (assign the given user)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        $values = array(
            'id' => $data['task_id'],
            'owner_id' => $data['owner_id'],
        );

        return $this->taskModificationModel->update($values);
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
        return $this->projectPermissionModel->isAssignable($this->getProjectId(), $data['owner_id']);
    }
}
