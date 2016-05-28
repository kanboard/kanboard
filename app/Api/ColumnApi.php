<?php

namespace Kanboard\Api;

/**
 * Column API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class ColumnApi extends BaseApi
{
    public function getColumns($project_id)
    {
        return $this->columnModel->getAll($project_id);
    }

    public function getColumn($column_id)
    {
        return $this->columnModel->getById($column_id);
    }

    public function updateColumn($column_id, $title, $task_limit = 0, $description = '')
    {
        return $this->columnModel->update($column_id, $title, $task_limit, $description);
    }

    public function addColumn($project_id, $title, $task_limit = 0, $description = '')
    {
        return $this->columnModel->create($project_id, $title, $task_limit, $description);
    }

    public function removeColumn($column_id)
    {
        return $this->columnModel->remove($column_id);
    }

    public function changeColumnPosition($project_id, $column_id, $position)
    {
        return $this->columnModel->changePosition($project_id, $column_id, $position);
    }
}
