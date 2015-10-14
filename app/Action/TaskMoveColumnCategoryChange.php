<?php

namespace Kanboard\Action;

use Kanboard\Model\Task;

/**
 * Move a task to another column when the category is changed
 *
 * @package action
 * @author  Francois Ferrand
 */
class TaskMoveColumnCategoryChange extends Base
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
            Task::EVENT_UPDATE,
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
            'column_id',
            'project_id',
            'category_id',
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
        $original_task = $this->taskFinder->getById($data['task_id']);

        return $this->taskPosition->movePosition(
            $data['project_id'],
            $data['task_id'],
            $this->getParam('dest_column_id'),
            $original_task['position'],
            $original_task['swimlane_id']
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
        return $data['column_id'] != $this->getParam('dest_column_id') && $data['category_id'] == $this->getParam('category_id');
    }
}
