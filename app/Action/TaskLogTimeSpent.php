<?php

namespace Action;

use Model\Task;
use Model\ProjectActivity;


/**
 * Logs the time a task spent in a specific column.
 *
 * @package action
 * @author  Oren Ben-Kiki
 */
class TaskLogTimeSpent extends Base
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
        return array('column_id' => t('Column'));
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return array('task_id', 'column_id');
    }

    /**
     * Execute the action (Add the time in the column to the tasks time_spent).
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool            True if the action was executed or false when not executed
     */
    public function doAction(array $data)
    {
        if (! $this->userSession->isLogged()) {
            return false;
        }
        
        $column = $this->board->getColumn($data['column_id']);

        $task = $this->taskFinder->getById($data['task_id']);
        $records = $this
            ->db
            ->table(ProjectActivity::TABLE)
            ->eq('event_name',Task::EVENT_MOVE_COLUMN)
            ->eq('project_id',$task['project_id'])
            ->eq('task_id',$task['id'])
            ->desc('date_creation')
            ->limit(2)
            ->findAllByColumn('date_creation');

        $time_spent = $records[0] - $records[1];
        $time_spent = ($time_spent / 60 / 60) + $task['time_spent'];

        return (bool) $this
            ->db
            ->table(Task::TABLE)
            ->eq('id',$task['id'])
            ->update(['time_spent' => $time_spent]);
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
        // only run the task if the task was moved out of the specified column
        $retval = ($data['column_id'] != $this->getParam('column_id'));
        if ($retval) {
            // now check if the previus column was our action column
            $task = $this->taskFinder->getById($data['task_id']);
            $data = $this
                ->db
                ->table(ProjectActivity::TABLE)
                ->eq('event_name',Task::EVENT_MOVE_COLUMN)
                ->eq('project_id',$task['project_id'])
                ->eq('task_id',$task['id'])
                ->desc('date_creation')
                ->limit(1)
                ->offset(1)
                ->findOneColumn('data');
            $data = json_decode($data);
            print_r($data);
            $retval = ($data->task->column_id == $this->getParam('column_id'));
        }
        
        return $retval;
    }
}
