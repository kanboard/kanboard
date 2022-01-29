<?php

namespace Kanboard\Action;

use Kanboard\Model\TaskModel;

/**
 * Move a task to another column when the category is changed
 *
 * @package Kanboard\Action
 * @author  Francois Ferrand
 */
class TaskMoveColumnCategoryChange extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Move the task to another column when the category is changed');
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
            TaskModel::EVENT_UPDATE,
            TaskModel::EVENT_CREATE,
            TaskModel::EVENT_CREATE_UPDATE,
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
            'dest_column_id' => t('Destination column'),
            'category_id' => t('Category'),
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
                'category_id',
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
            $this->getParam('dest_column_id'),
            $data['task']['position'],
            $data['task']['swimlane_id'],
            false
        );
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
        return $data['task']['column_id'] != $this->getParam('dest_column_id') && $data['task']['category_id'] == $this->getParam('category_id');
    }
}
