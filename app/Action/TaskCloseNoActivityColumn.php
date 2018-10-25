<?php

namespace Kanboard\Action;

use Kanboard\Model\TaskModel;

/**
 * Close automatically a task after inactive and in a defined column
 *
 * @package Kanboard\Action
 * @author  Frederic Guillot
 */
class TaskCloseNoActivityColumn extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Close a task when there is no activity in a specific column');
    }

    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return array(TaskModel::EVENT_DAILY_CRONJOB);
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
            'duration' => t('Duration in days'),
            'column_id' => t('Column')
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
        return array('tasks');
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
        $results = array();
        $max = $this->getParam('duration') * 86400;

        foreach ($data['tasks'] as $task) {
            $duration = time() - $task['date_modification'];

            if ($duration > $max && $task['column_id'] == $this->getParam('column_id')) {
                $results[] = $this->taskStatusModel->close($task['id']);
            }
        }

        return in_array(true, $results, true);
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
        return count($data['tasks']) > 0;
    }
}
