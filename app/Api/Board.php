<?php

namespace Kanboard\Api;

/**
 * Board API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class Board extends Base
{
    public function getBoard($project_id)
    {
        $this->checkProjectPermission($project_id);
        return $this->board->getBoard($project_id);
    }

    public function getColumns($project_id)
    {
        return $this->board->getColumns($project_id);
    }

    public function getColumn($column_id)
    {
        return $this->board->getColumn($column_id);
    }

    public function moveColumnUp($project_id, $column_id)
    {
        return $this->board->moveUp($project_id, $column_id);
    }

    public function moveColumnDown($project_id, $column_id)
    {
        return $this->board->moveDown($project_id, $column_id);
    }

    public function updateColumn($column_id, $title, $task_limit = 0, $description = '')
    {
        return $this->board->updateColumn($column_id, $title, $task_limit, $description);
    }

    public function addColumn($project_id, $title, $task_limit = 0, $description = '')
    {
        return $this->board->addColumn($project_id, $title, $task_limit, $description);
    }

    public function removeColumn($column_id)
    {
        return $this->board->removeColumn($column_id);
    }
}
