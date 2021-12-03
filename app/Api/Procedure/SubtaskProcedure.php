<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\SubtaskAuthorization;
use Kanboard\Api\Authorization\TaskAuthorization;

/**
 * Subtask API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class SubtaskProcedure extends BaseProcedure
{
    public function getSubtask($subtask_id)
    {
        SubtaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getSubtask', $subtask_id);
        return $this->subtaskModel->getById($subtask_id);
    }

    public function getAllSubtasks($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getAllSubtasks', $task_id);
        return $this->subtaskModel->getAll($task_id);
    }

    public function removeSubtask($subtask_id)
    {
        SubtaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeSubtask', $subtask_id);
        return $this->subtaskModel->remove($subtask_id);
    }

    public function createSubtask($task_id, $title, $user_id = 0, $time_estimated = 0, $time_spent = 0, $status = 0)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'createSubtask', $task_id);
        
        $values = array(
            'title' => $title,
            'task_id' => $task_id,
            'user_id' => $user_id,
            'time_estimated' => $time_estimated,
            'time_spent' => $time_spent,
            'status' => $status,
        );

        list($valid, ) = $this->subtaskValidator->validateCreation($values);
        return $valid ? $this->subtaskModel->create($values) : false;
    }

    public function updateSubtask($id, $task_id, $title = null, $user_id = null, $time_estimated = null, $time_spent = null, $status = null, $position = null)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateSubtask', $task_id);
        
        $values = array(
            'id' => $id,
            'task_id' => $task_id,
            'title' => $title,
            'user_id' => $user_id,
            'time_estimated' => $time_estimated,
            'time_spent' => $time_spent,
            'status' => $status,
            'position' => $position
        );

        foreach ($values as $key => $value) {
            if (is_null($value)) {
                unset($values[$key]);
            }
        }

        list($valid, ) = $this->subtaskValidator->validateApiModification($values);
        return $valid && $this->subtaskModel->update($values);
    }
}
