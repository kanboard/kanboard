<?php

namespace Api;

use Model\Task as TaskModel;

/**
 * Task API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Task extends Base
{
    public function getTask($task_id)
    {
        return $this->formatTask($this->taskFinder->getById($task_id));
    }

    public function getTaskByReference($project_id, $reference)
    {
        return $this->formatTask($this->taskFinder->getByReference($project_id, $reference));
    }

    public function getAllTasks($project_id, $status_id = TaskModel::STATUS_OPEN)
    {
        return $this->formatTasks($this->taskFinder->getAll($project_id, $status_id));
    }

    public function getOverdueTasks()
    {
        return $this->taskFinder->getOverdueTasks();
    }

    public function openTask($task_id)
    {
        return $this->taskStatus->open($task_id);
    }

    public function closeTask($task_id)
    {
        return $this->taskStatus->close($task_id);
    }

    public function removeTask($task_id)
    {
        return $this->task->remove($task_id);
    }

    public function moveTaskPosition($project_id, $task_id, $column_id, $position, $swimlane_id = 0)
    {
        return $this->taskPosition->movePosition($project_id, $task_id, $column_id, $position, $swimlane_id);
    }

    public function createTask($title, $project_id, $color_id = '', $column_id = 0, $owner_id = 0, $creator_id = 0,
                               $date_due = '', $description = '', $category_id = 0, $score = 0, $swimlane_id = 0,
                               $recurrence_status = 0, $recurrence_trigger = 0, $recurrence_factor = 0, $recurrence_timeframe = 0,
                               $recurrence_basedate = 0, $reference = '')
    {
        $values = array(
            'title' => $title,
            'project_id' => $project_id,
            'color_id' => $color_id,
            'column_id' => $column_id,
            'owner_id' => $owner_id,
            'creator_id' => $creator_id,
            'date_due' => $date_due,
            'description' => $description,
            'category_id' => $category_id,
            'score' => $score,
            'swimlane_id' => $swimlane_id,
            'recurrence_status' => $recurrence_status,
            'recurrence_trigger' => $recurrence_trigger,
            'recurrence_factor' => $recurrence_factor,
            'recurrence_timeframe' => $recurrence_timeframe,
            'recurrence_basedate' => $recurrence_basedate,
            'reference' => $reference,
        );

        list($valid,) = $this->taskValidator->validateCreation($values);

        return $valid ? $this->taskCreation->create($values) : false;
    }

    public function updateTask($id, $title = null, $project_id = null, $color_id = null, $owner_id = null,
                               $creator_id = null, $date_due = null, $description = null, $category_id = null, $score = null,
                               $recurrence_status = null, $recurrence_trigger = null, $recurrence_factor = null,
                               $recurrence_timeframe = null, $recurrence_basedate = null, $reference = null)
    {
        $values = array(
            'id' => $id,
            'title' => $title,
            'project_id' => $project_id,
            'color_id' => $color_id,
            'owner_id' => $owner_id,
            'creator_id' => $creator_id,
            'date_due' => $date_due,
            'description' => $description,
            'category_id' => $category_id,
            'score' => $score,
            'recurrence_status' => $recurrence_status,
            'recurrence_trigger' => $recurrence_trigger,
            'recurrence_factor' => $recurrence_factor,
            'recurrence_timeframe' => $recurrence_timeframe,
            'recurrence_basedate' => $recurrence_basedate,
            'reference' => $reference,
        );

        foreach ($values as $key => $value) {
            if (is_null($value)) {
                unset($values[$key]);
            }
        }

        list($valid) = $this->taskValidator->validateApiModification($values);
        return $valid && $this->taskModification->update($values);
    }

    private function formatTask($task)
    {
        if (! empty($task)) {
            $task['url'] = $this->helper->url->to('task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), '', true);
        }

        return $task;
    }

    private function formatTasks($tasks)
    {
        if (! empty($tasks)) {
            foreach ($tasks as &$task) {
                $task = $this->formatTask($task);
            }
        }

        return $tasks;
    }
}
