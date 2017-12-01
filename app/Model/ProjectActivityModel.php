<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use PicoDb\Table;

/**
 * Project activity model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class ProjectActivityModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'project_activities';

    /**
     * Add a new event for the project
     *
     * @access public
     * @param  integer     $project_id      Project id
     * @param  integer     $task_id         Task id
     * @param  integer     $creator_id      User id
     * @param  string      $event_name      Event name
     * @param  array       $data            Event data (will be serialized)
     * @return boolean
     */
    public function createEvent($project_id, $task_id, $creator_id, $event_name, array $data)
    {
        return $this->db->table(self::TABLE)->insert(array(
            'project_id' => $project_id,
            'task_id' => $task_id,
            'creator_id' => $creator_id,
            'event_name' => $event_name,
            'date_creation' => time(),
            'data' => json_encode($data),
        ));
    }

    /**
     * Get query
     *
     * @access public
     * @return Table
     */
    public function getQuery()
    {
        return $this
            ->db
            ->table(ProjectActivityModel::TABLE)
            ->columns(
                ProjectActivityModel::TABLE.'.*',
                'uc.username AS author_username',
                'uc.name AS author_name',
                'uc.email',
                'uc.avatar_path'
            )
            ->join(TaskModel::TABLE, 'id', 'task_id')
            ->join(ProjectModel::TABLE, 'id', 'project_id')
            ->left(UserModel::TABLE, 'uc', 'id', ProjectActivityModel::TABLE, 'creator_id');
    }

    /**
     * Remove old event entries to avoid large table
     *
     * @access public
     * @param  integer $ts Timestamp
     */
    public function cleanup($ts)
    {
        $this->db->table(self::TABLE)->lt('date_creation', $ts)->remove();
    }
}
