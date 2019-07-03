<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

class TaskReorderModel extends Base
{
    public function reorderByPriority($projectID, $swimlaneID, $columnID, $direction)
    {
        $this->db->startTransaction();

        $taskIDs = $this->db->table(TaskModel::TABLE)
            ->eq('project_id', $projectID)
            ->eq('swimlane_id', $swimlaneID)
            ->eq('column_id', $columnID)
            ->orderBy('priority', $direction)
            ->asc('id')
            ->findAllByColumn('id');

        $this->reorderTasks($taskIDs);

        $this->db->closeTransaction();
    }

    public function reorderByAssigneeAndPriority($projectID, $swimlaneID, $columnID, $direction)
    {
        $this->db->startTransaction();

        $taskIDs = $this->db->table(TaskModel::TABLE)
            ->eq('tasks.project_id', $projectID)
            ->eq('tasks.swimlane_id', $swimlaneID)
            ->eq('tasks.column_id', $columnID)
            ->asc('u.name')
            ->asc('u.username')
            ->orderBy('tasks.priority', $direction)
            ->left(UserModel::TABLE, 'u', 'id', TaskModel::TABLE, 'owner_id')
            ->findAllByColumn('tasks.id');

        $this->reorderTasks($taskIDs);

        $this->db->closeTransaction();
    }

    public function reorderByAssignee($projectID, $swimlaneID, $columnID, $direction)
    {
        $this->db->startTransaction();

        $taskIDs = $this->db->table(TaskModel::TABLE)
            ->eq('tasks.project_id', $projectID)
            ->eq('tasks.swimlane_id', $swimlaneID)
            ->eq('tasks.column_id', $columnID)
            ->orderBy('u.name', $direction)
            ->orderBy('u.username', $direction)
            ->orderBy('u.id', $direction)
            ->left(UserModel::TABLE, 'u', 'id', TaskModel::TABLE, 'owner_id')
            ->findAllByColumn('tasks.id');

        $this->reorderTasks($taskIDs);

        $this->db->closeTransaction();
    }

    public function reorderByDueDate($projectID, $swimlaneID, $columnID, $direction)
    {
        $this->db->startTransaction();

        $taskIDs = $this->db->table(TaskModel::TABLE)
            ->eq('project_id', $projectID)
            ->eq('swimlane_id', $swimlaneID)
            ->eq('column_id', $columnID)
            ->orderBy('date_due', $direction)
            ->asc('id')
            ->findAllByColumn('id');

        $this->reorderTasks($taskIDs);

        $this->db->closeTransaction();
    }

    protected function reorderTasks(array $taskIDs)
    {
        $i = 1;
        foreach ($taskIDs as $taskID) {
            $this->db->table(TaskModel::TABLE)
                ->eq('id', $taskID)
                ->update(['position' => $i]);
            $i++;
        }
    }
}
