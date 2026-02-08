<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\TaskAuthorization;

/**
 * Class TaskMetadataProcedure
 *
 * @package Kanboard\Api\Procedure
 * @author  Frederic Guillot
 */
class TaskMetadataProcedure extends BaseProcedure
{
    public function getTaskMetadata($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getTaskMetadata', $task_id);
        return (object) $this->taskMetadataModel->getAll($task_id);
    }

    public function getTaskMetadataByName($task_id, $name)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getTaskMetadataByName', $task_id);
        return $this->taskMetadataModel->get($task_id, $name);
    }

    public function saveTaskMetadata($task_id, array $values)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'saveTaskMetadata', $task_id);
        return $this->taskMetadataModel->save($task_id, $values);
    }

    public function removeTaskMetadata($task_id, $name)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeTaskMetadata', $task_id);
        return $this->taskMetadataModel->remove($task_id, $name);
    }
}
