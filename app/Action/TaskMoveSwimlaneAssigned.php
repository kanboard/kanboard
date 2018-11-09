<?php

namespace Kanboard\Action;

use Kanboard\Model\TaskModel;

/**
 * Move a task to another column when an assignee is set
 *
 * @package Kanboard\Action
 * @author  Francois Ferrand
 */
class TaskMoveSwimlaneAssigned extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Move the task to another swimlane when assigned to a user');
    }

    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array(
            
            TaskModel::EVENT_CREATE_UPDATE,           
            TaskModel::EVENT_ASSIGNEE_CHANGE,
            TaskModel::EVENT_UPDATE,
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
        return array(
            'user_id' => t('Assignee'),
            'dest_swimlane_id' => t('Destination swimlane'),
	
        );
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
            'task' => array(
                'project_id',
                'column_id',
                'owner_id',
                'position',
                'swimlane_id',
            )
        );
    }

    /**
     * Execute the action (move the task to another column)
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        return $this->taskPositionModel->movePosition(
            $data['task']['project_id'],
            $data['task_id'],
            $data['task']['column_id'],
            $data['task']['position'],
            $this->getParam('dest_swimlane_id'),
            true
        );
    }

    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     **/
    public function hasRequiredCondition(array $data)
    {
        return $data['task']['swimlane_id'] != $this->getParam('dest_swimlane_id') && $data['task']['owner_id'] == $this->getParam("user_id");
    }
}
