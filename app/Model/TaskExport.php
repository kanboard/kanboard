<?php

namespace Model;

use PDO;

/**
 * Task Export model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskExport extends Base
{
    /**
     * Fetch tasks and return the prepared CSV
     *
     * @access public
     * @param  integer    $project_id      Project id
     * @param  mixed      $from            Start date (timestamp or user formatted date)
     * @param  mixed      $to              End date (timestamp or user formatted date)
     * @return array
     */
    public function export($project_id, $from, $to)
    {
        $tasks = $this->getTasks($project_id, $from, $to);
        $swimlanes = $this->swimlane->getSwimlanesList($project_id);
        $results = array($this->getColumns());

        foreach ($tasks as &$task) {
            $results[] = array_values($this->format($task, $swimlanes));
        }

        return $results;
    }

    /**
     * Get the list of tasks for a given project and date range
     *
     * @access public
     * @param  integer    $project_id      Project id
     * @param  mixed      $from            Start date (timestamp or user formatted date)
     * @param  mixed      $to              End date (timestamp or user formatted date)
     * @return array
     */
    public function getTasks($project_id, $from, $to)
    {
        $sql = '
            SELECT
            tasks.id,
            projects.name AS project_name,
            tasks.is_active,
            project_has_categories.name AS category_name,
            tasks.swimlane_id,
            columns.title AS column_title,
            tasks.position,
            tasks.color_id,
            tasks.date_due,
            creators.username AS creator_username,
            users.username AS assignee_username,
            tasks.score,
            tasks.title,
            tasks.date_creation,
            tasks.date_modification,
            tasks.date_completed,
            tasks.date_started,
            tasks.time_estimated,
            tasks.time_spent
            FROM tasks
            LEFT JOIN users ON users.id = tasks.owner_id
            LEFT JOIN users AS creators ON creators.id = tasks.creator_id
            LEFT JOIN project_has_categories ON project_has_categories.id = tasks.category_id
            LEFT JOIN columns ON columns.id = tasks.column_id
            LEFT JOIN projects ON projects.id = tasks.project_id
            WHERE tasks.date_creation >= ? AND tasks.date_creation <= ? AND tasks.project_id = ?
            ORDER BY tasks.id ASC
        ';

        if (! is_numeric($from)) {
            $from = $this->dateParser->resetDateToMidnight($this->dateParser->getTimestamp($from));
        }

        if (! is_numeric($to)) {
            $to = $this->dateParser->resetDateToMidnight(strtotime('+1 day', $this->dateParser->getTimestamp($to)));
        }

        $rq = $this->db->execute($sql, array($from, $to, $project_id));
        return $rq->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Format the output of a task array
     *
     * @access public
     * @param  array     $task        Task properties
     * @param  array     $swimlanes   List of swimlanes
     * @return array
     */
    public function format(array &$task, array &$swimlanes)
    {
        $colors = $this->color->getList();

        $task['is_active'] = $task['is_active'] == Task::STATUS_OPEN ? e('Open') : e('Closed');
        $task['color_id'] = $colors[$task['color_id']];
        $task['score'] = $task['score'] ?: 0;
        $task['swimlane_id'] = isset($swimlanes[$task['swimlane_id']]) ? $swimlanes[$task['swimlane_id']] : '?';

        $this->dateParser->format($task, array('date_due', 'date_modification', 'date_creation', 'date_started', 'date_completed'), 'Y-m-d');

        return $task;
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
            e('Task Id'),
            e('Project'),
            e('Status'),
            e('Category'),
            e('Swimlane'),
            e('Column'),
            e('Position'),
            e('Color'),
            e('Due date'),
            e('Creator'),
            e('Assignee'),
            e('Complexity'),
            e('Title'),
            e('Creation date'),
            e('Modification date'),
            e('Completion date'),
            e('Start date'),
            e('Time estimated'),
            e('Time spent'),
        );
    }
}
