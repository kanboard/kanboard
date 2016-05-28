<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Transition
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class TransitionModel extends Base
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
     * @param  integer $user_id
     * @param  array   $task_event
     * @return bool
     */
    public function save($user_id, array $task_event)
    {
        $time = time();

        return $this->db->table(self::TABLE)->insert(array(
            'user_id' => $user_id,
            'project_id' => $task_event['project_id'],
            'task_id' => $task_event['task_id'],
            'src_column_id' => $task_event['src_column_id'],
            'dst_column_id' => $task_event['dst_column_id'],
            'date' => $time,
            'time_spent' => $time - $task_event['date_moved']
        ));
    }

    /**
     * Get time spent by task for each column
     *
     * @access public
     * @param  integer  $task_id
     * @return array
     */
    public function getTimeSpentByTask($task_id)
    {
        return $this->db
                    ->hashtable(self::TABLE)
                    ->groupBy('src_column_id')
                    ->eq('task_id', $task_id)
                    ->getAll('src_column_id', 'SUM(time_spent) AS time_spent');
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
                            UserModel::TABLE.'.name',
                            UserModel::TABLE.'.username',
                            self::TABLE.'.user_id',
                            self::TABLE.'.date',
                            self::TABLE.'.time_spent'
                        )
                        ->eq('task_id', $task_id)
                        ->desc('date')
                        ->join(UserModel::TABLE, 'id', 'user_id')
                        ->join(ColumnModel::TABLE.' as src', 'id', 'src_column_id', self::TABLE, 'src')
                        ->join(ColumnModel::TABLE.' as dst', 'id', 'dst_column_id', self::TABLE, 'dst')
                        ->findAll();
    }

    /**
     * Get all transitions by project
     *
     * @access public
     * @param  integer    $project_id
     * @param  mixed      $from            Start date (timestamp or user formatted date)
     * @param  mixed      $to              End date (timestamp or user formatted date)
     * @return array
     */
    public function getAllByProjectAndDate($project_id, $from, $to)
    {
        if (! is_numeric($from)) {
            $from = $this->dateParser->removeTimeFromTimestamp($this->dateParser->getTimestamp($from));
        }

        if (! is_numeric($to)) {
            $to = $this->dateParser->removeTimeFromTimestamp(strtotime('+1 day', $this->dateParser->getTimestamp($to)));
        }

        return $this->db->table(self::TABLE)
                        ->columns(
                            TaskModel::TABLE.'.id',
                            TaskModel::TABLE.'.title',
                            'src.title as src_column',
                            'dst.title as dst_column',
                            UserModel::TABLE.'.name',
                            UserModel::TABLE.'.username',
                            self::TABLE.'.user_id',
                            self::TABLE.'.date',
                            self::TABLE.'.time_spent'
                        )
                        ->gte('date', $from)
                        ->lte('date', $to)
                        ->eq(self::TABLE.'.project_id', $project_id)
                        ->desc('date')
                        ->desc(self::TABLE.'.id')
                        ->join(TaskModel::TABLE, 'id', 'task_id')
                        ->join(UserModel::TABLE, 'id', 'user_id')
                        ->join(ColumnModel::TABLE.' as src', 'id', 'src_column_id', self::TABLE, 'src')
                        ->join(ColumnModel::TABLE.' as dst', 'id', 'dst_column_id', self::TABLE, 'dst')
                        ->findAll();
    }
}
