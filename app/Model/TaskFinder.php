<?php

namespace Model;

/**
 * Task Finder model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskFinder extends Base
{
    private function prepareRequest()
    {
        return $this->db
            ->table(Task::TABLE)
            ->columns(
                '(SELECT count(*) FROM comments WHERE task_id=tasks.id) AS nb_comments',
                '(SELECT count(*) FROM task_has_files WHERE task_id=tasks.id) AS nb_files',
                '(SELECT count(*) FROM task_has_subtasks WHERE task_id=tasks.id) AS nb_subtasks',
                '(SELECT count(*) FROM task_has_subtasks WHERE task_id=tasks.id AND status=2) AS nb_completed_subtasks',
                'tasks.id',
                'tasks.reference',
                'tasks.title',
                'tasks.description',
                'tasks.date_creation',
                'tasks.date_modification',
                'tasks.date_completed',
                'tasks.date_due',
                'tasks.color_id',
                'tasks.project_id',
                'tasks.column_id',
                'tasks.owner_id',
                'tasks.creator_id',
                'tasks.position',
                'tasks.is_active',
                'tasks.score',
                'tasks.category_id',
                'users.username AS assignee_username',
                'users.name AS assignee_name'
            )
            ->join(User::TABLE, 'id', 'owner_id');
    }

    public function search($project_id, $search, $offset = 0, $limit = 25, $column = 'tasks.id', $direction = 'DESC')
    {
        return $this->prepareRequest()
                    ->eq('project_id', $project_id)
                    ->like('title', '%'.$search.'%')
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy($column, $direction)
                    ->findAll();
    }

    public function countSearch($project_id, $search)
    {
        return $this->db->table(Task::TABLE)
                        ->eq('project_id', $project_id)
                        ->like('title', '%'.$search.'%')
                        ->count();
    }

    public function getClosedTasks($project_id, $offset = 0, $limit = 25, $column = 'tasks.date_completed', $direction = 'DESC')
    {
        return $this->prepareRequest()
                    ->eq('project_id', $project_id)
                    ->eq('is_active', Task::STATUS_CLOSED)
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy($column, $direction)
                    ->findAll();
    }

    public function getOpenTasks($project_id, $column = 'tasks.position', $direction = 'ASC')
    {
        return $this->prepareRequest()
                    ->eq('project_id', $project_id)
                    ->eq('is_active', Task::STATUS_OPEN)
                    ->orderBy($column, $direction)
                    ->findAll();
    }
}
