<?php

namespace Kanboard\Export;

use Kanboard\Core\Base;
use Kanboard\Model\TaskModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\UserModel;

/**
 * Subtask Export
 *
 * @package  export
 * @author   Frederic Guillot
 */
class SubtaskExport extends Base
{
    /**
     * Subtask statuses
     *
     * @access private
     * @var array
     */
    private $subtask_status = array();

    /**
     * Fetch subtasks and return the prepared CSV
     *
     * @access public
     * @param  integer    $project_id      Project id
     * @param  mixed      $from            Start date (timestamp or user formatted date)
     * @param  mixed      $to              End date (timestamp or user formatted date)
     * @return array
     */
    public function export($project_id, $from, $to)
    {
        $this->subtask_status = $this->subtaskModel->getStatusList();
        $subtasks = $this->getSubtasks($project_id, $from, $to);
        $results = array($this->getColumns());

        foreach ($subtasks as $subtask) {
            $results[] = $this->format($subtask);
        }

        return $results;
    }

    /**
     * Get column titles
     *
     * @access public
     * @return string[]
     */
    public function getColumns()
    {
        return array(
            e('Subtask Id'),
            e('Title'),
            e('Status'),
            e('Assignee'),
            e('Time estimated'),
            e('Time spent'),
            e('Task Id'),
            e('Task Title'),
        );
    }

    /**
     * Format the output of a subtask array
     *
     * @access public
     * @param  array     $subtask        Subtask properties
     * @return array
     */
    public function format(array $subtask)
    {
        $values = array();
        $values[] = $subtask['id'];
        $values[] = $subtask['title'];
        $values[] = $this->subtask_status[$subtask['status']];
        $values[] = $subtask['assignee_name'] ?: $subtask['assignee_username'];
        $values[] = $subtask['time_estimated'];
        $values[] = $subtask['time_spent'];
        $values[] = $subtask['task_id'];
        $values[] = $subtask['task_title'];

        return $values;
    }

    /**
     * Get all subtasks for a given project
     *
     * @access public
     * @param  integer   $project_id    Project id
     * @param  mixed     $from          Start date (timestamp or user formatted date)
     * @param  mixed     $to            End date (timestamp or user formatted date)
     * @return array
     */
    public function getSubtasks($project_id, $from, $to)
    {
        if (! is_numeric($from)) {
            $from = $this->dateParser->removeTimeFromTimestamp($this->dateParser->getTimestamp($from));
        }

        if (! is_numeric($to)) {
            $to = $this->dateParser->removeTimeFromTimestamp(strtotime('+1 day', $this->dateParser->getTimestamp($to)));
        }

        return $this->db->table(SubtaskModel::TABLE)
                        ->eq('project_id', $project_id)
                        ->columns(
                            SubtaskModel::TABLE.'.*',
                            UserModel::TABLE.'.username AS assignee_username',
                            UserModel::TABLE.'.name AS assignee_name',
                            TaskModel::TABLE.'.title AS task_title'
                        )
                        ->gte('date_creation', $from)
                        ->lte('date_creation', $to)
                        ->join(TaskModel::TABLE, 'id', 'task_id')
                        ->join(UserModel::TABLE, 'id', 'user_id')
                        ->asc(SubtaskModel::TABLE.'.id')
                        ->findAll();
    }
}
