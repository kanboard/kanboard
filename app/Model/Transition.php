<?php

namespace Model;

/**
 * Transition model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class Transition extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'transitions';

    /**
     * Save transition event
     *
     * @access public
     * @param  integer  $user_id
     * @param  array    $task
     * @return boolean
     */
    public function save($user_id, array $task)
    {
        return $this->db->table(self::TABLE)->insert(array(
            'user_id' => $user_id,
            'project_id' => $task['project_id'],
            'task_id' => $task['task_id'],
            'src_column_id' => $task['src_column_id'],
            'dst_column_id' => $task['dst_column_id'],
            'date' => time(),
            'time_spent' => time() - $task['date_moved']
        ));
    }

    /**
     * Get all transitions by task
     *
     * @access public
     * @param  integer   $task_id
     * @return array
     */
    public function getAllByTask($task_id)
    {
        return $this->db->table(self::TABLE)
                        ->columns(
                            'src.title as src_column',
                            'dst.title as dst_column',
                            User::TABLE.'.name',
                            User::TABLE.'.username',
                            self::TABLE.'.user_id',
                            self::TABLE.'.date',
                            self::TABLE.'.time_spent'
                        )
                        ->eq('task_id', $task_id)
                        ->desc('date')
                        ->join(User::TABLE, 'id', 'user_id')
                        ->join(Board::TABLE.' as src', 'id', 'src_column_id', self::TABLE, 'src')
                        ->join(Board::TABLE.' as dst', 'id', 'dst_column_id', self::TABLE, 'dst')
                        ->findAll();
    }
}
