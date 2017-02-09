<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ColumnAuthorization;
use Kanboard\Api\Authorization\ProjectAuthorization;
use Kanboard\Model\TaskModel;

/**
 * Column API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class ColumnProcedure extends BaseProcedure
{
    public function getColumns($project_id)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'getColumns', $project_id);
        return $this->columnModel->getAll($project_id);
    }

    public function getColumn($column_id)
    {
        ColumnAuthorization::getInstance($this->container)->check($this->getClassName(), 'getColumn', $column_id);
        return $this->columnModel->getById($column_id);
    }

    public function updateColumn($column_id, $title, $task_limit = 0, $description = '')
    {
        ColumnAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateColumn', $column_id);
        return $this->columnModel->update($column_id, $title, $task_limit, $description);
    }

    public function addColumn($project_id, $title, $task_limit = 0, $description = '')
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'addColumn', $project_id);
        return $this->columnModel->create($project_id, $title, $task_limit, $description);
    }

    public function removeColumn($column_id)
    {
        ColumnAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeColumn', $column_id);

        $projectId = $this->columnModel->getProjectId($column_id);
        $nbTasks = $this->taskFinderModel->countByColumnId($projectId, $column_id, array(TaskModel::STATUS_OPEN, TaskModel::STATUS_CLOSED));

        if ($nbTasks > 0) {
            $this->logger->error(__METHOD__.': This column cannot be removed because it contains '.$nbTasks.' tasks');
            return false;
        }

        return $this->columnModel->remove($column_id);
    }

    public function changeColumnPosition($project_id, $column_id, $position)
    {
        ProjectAuthorization::getInstance($this->container)->check($this->getClassName(), 'changeColumnPosition', $project_id);
        return $this->columnModel->changePosition($project_id, $column_id, $position);
    }
}
