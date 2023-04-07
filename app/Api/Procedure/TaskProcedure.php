<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProjectAuthorization;
use Kanboard\Api\Authorization\TaskAuthorization;
use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Model\TaskModel;

/**
 * Task API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class TaskProcedure extends BaseProcedure
{
    public function searchTasks($project_id, $query)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'searchTasks', $project_id);
        return $this->taskLexer->build($query)->withFilter(new TaskProjectFilter($project_id))->toArray();
    }

    public function getTask($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getTask', $task_id);
        $task = $this->taskFinderModel->getById($task_id);
        return $this->taskApiFormatter->withTask($task)->format();
    }

    public function getTaskByReference($project_id, $reference)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getTaskByReference', $project_id);
        $task = $this->taskFinderModel->getByReference($project_id, $reference);
        return $this->taskApiFormatter->withTask($task)->format();
    }

    public function getAllTasks($project_id, $status_id = TaskModel::STATUS_OPEN)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getAllTasks', $project_id);
        $tasks = $this->taskFinderModel->getAll($project_id, $status_id);
        return $this->tasksApiFormatter->withTasks($tasks)->format();
    }

    public function getOverdueTasks()
    {
        return $this->taskFinderModel->getOverdueTasks();
    }

    public function getOverdueTasksByProject($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getOverdueTasksByProject', $project_id);
        return $this->taskFinderModel->getOverdueTasksByProject($project_id);
    }

    public function openTask($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'openTask', $task_id);
        return $this->taskStatusModel->open($task_id);
    }

    public function closeTask($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'closeTask', $task_id);
        return $this->taskStatusModel->close($task_id);
    }

    public function removeTask($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeTask', $task_id);
        return $this->taskModel->remove($task_id);
    }

    public function moveTaskPosition($project_id, $task_id, $column_id, $position, $swimlane_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'moveTaskPosition', $project_id);
        return $this->taskPositionModel->movePosition($project_id, $task_id, $column_id, $position, $swimlane_id, true, false);
    }

    public function moveTaskToProject($task_id, $project_id, $swimlane_id = null, $column_id = null, $category_id = null, $owner_id = null)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'moveTaskToProject', $project_id);
        return $this->taskProjectMoveModel->moveToProject($task_id, $project_id, $swimlane_id, $column_id, $category_id, $owner_id);
    }

    public function duplicateTaskToProject($task_id, $project_id, $swimlane_id = null, $column_id = null, $category_id = null, $owner_id = null)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'duplicateTaskToProject', $project_id);
        return $this->taskProjectDuplicationModel->duplicateToProject($task_id, $project_id, $swimlane_id, $column_id, $category_id, $owner_id);
    }

    public function createTask($title, $project_id, $color_id = '', $column_id = 0, $owner_id = 0, $creator_id = 0,
                               $date_due = '', $description = '', $category_id = 0, $score = 0, $swimlane_id = null, $priority = 0,
                               $recurrence_status = 0, $recurrence_trigger = 0, $recurrence_factor = 0, $recurrence_timeframe = 0,
                               $recurrence_basedate = 0, $reference = '', array $tags = array(), $date_started = '',
                               $time_spent = null, $time_estimated = null)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'createTask', $project_id);

        if ($owner_id !== 0 && ! $this->projectPermissionModel->isAssignable($project_id, $owner_id)) {
            return false;
        }

        if ($this->userSession->isLogged()) {
            $creator_id = $this->userSession->getId();
        }

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
            'priority' => $priority,
            'tags' => $tags,
            'date_started' => $date_started,
            'time_spent' => $time_spent,
            'time_estimated' => $time_estimated,
        );

        list($valid, ) = $this->taskValidator->validateCreation($values);

        return $valid ? $this->taskCreationModel->create($values) : false;
    }

    public function updateTask($id, $title = null, $color_id = null, $owner_id = null,
                               $date_due = null, $description = null, $category_id = null, $score = null, $priority = null,
                               $recurrence_status = null, $recurrence_trigger = null, $recurrence_factor = null,
                               $recurrence_timeframe = null, $recurrence_basedate = null, $reference = null, $tags = null, $date_started = null,
                               $time_spent = null, $time_estimated = null)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateTask', $id);
        $project_id = $this->taskFinderModel->getProjectId($id);

        if ($project_id === 0) {
            return false;
        }

        if ($owner_id !== null && $owner_id != 0 && ! $this->projectPermissionModel->isAssignable($project_id, $owner_id)) {
            return false;
        }

        $values = $this->filterValues(array(
            'id' => $id,
            'title' => $title,
            'color_id' => $color_id,
            'owner_id' => $owner_id,
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
            'priority' => $priority,
            'tags' => $tags,
            'date_started' => $date_started,
            'time_spent' => $time_spent,
            'time_estimated' => $time_estimated,
        ));

        list($valid) = $this->taskValidator->validateApiModification($values);
        return $valid && $this->taskModificationModel->update($values);
    }
}
