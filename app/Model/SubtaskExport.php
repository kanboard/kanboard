<?php

namespace Model;

/**
 * Subtask Export
 *
 * @package  model
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
        $this->subtask_status = $this->subTask->getStatusList();
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
            $from = $this->dateParser->resetDateToMidnight($this->dateParser->getTimestamp($from));
        }

        if (! is_numeric($to)) {
            $to = $this->dateParser->resetDateToMidnight(strtotime('+1 day', $this->dateParser->getTimestamp($to)));
        }

        return $this->db->table(SubTask::TABLE)
                        ->eq('project_id', $project_id)
                        ->columns(
                            SubTask::TABLE.'.*',
                            User::TABLE.'.username AS assignee_username',
                            User::TABLE.'.name AS assignee_name',
                            Task::TABLE.'.title AS task_title'
                        )
                        ->gte('date_creation', $from)
                        ->lte('date_creation', $to)
                        ->join(Task::TABLE, 'id', 'task_id')
                        ->join(User::TABLE, 'id', 'user_id')
                        ->asc(SubTask::TABLE.'.id')
                        ->findAll();
    }
}
