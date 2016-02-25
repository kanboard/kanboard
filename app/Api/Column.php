<?php

namespace Kanboard\Api;

/**
 * Column API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Column extends Base
{
    public function getColumns($project_id)
    {
        return $this->column->getAll($project_id);
    }

    public function getColumn($column_id)
    {
        return $this->column->getById($column_id);
    }

    public function updateColumn($column_id, $title, $task_limit = 0, $description = '')
    {
        return $this->column->update($column_id, $title, $task_limit, $description);
    }

    public function addColumn($project_id, $title, $task_limit = 0, $description = '')
    {
        return $this->column->create($project_id, $title, $task_limit, $description);
    }

    public function removeColumn($column_id)
    {
        return $this->column->remove($column_id);
    }

    public function changeColumnPosition($project_id, $column_id, $position)
    {
        return $this->column->changePosition($project_id, $column_id, $position);
    }
}
