<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\TaskAuthorization;

/**
 * Class TaskTagProcedure
 *
 * @package Kanboard\Api\Procedure
 * @author  Frederic Guillot
 */
class TaskTagProcedure extends BaseProcedure
{
    public function setTaskTags($project_id, $task_id, array $tags)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'setTaskTags', $task_id);
        return $this->taskTagModel->save($project_id, $task_id, $tags);
    }

    public function getTaskTags($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getTaskTags', $task_id);
        return (object) $this->taskTagModel->getList($task_id);
    }
}
