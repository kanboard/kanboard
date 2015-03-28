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
                            Task::TABLE.'.id',
                            Task::TABLE.'.title',
                            'src.title as src_column',
                            'dst.title as dst_column',
                            User::TABLE.'.name',
                            User::TABLE.'.username',
                            self::TABLE.'.user_id',
                            self::TABLE.'.date',
                            self::TABLE.'.time_spent'
                        )
                        ->gte('date', $from)
                        ->lte('date', $to)
                        ->eq(self::TABLE.'.project_id', $project_id)
                        ->desc('date')
                        ->join(Task::TABLE, 'id', 'task_id')
                        ->join(User::TABLE, 'id', 'user_id')
                        ->join(Board::TABLE.' as src', 'id', 'src_column_id', self::TABLE, 'src')
                        ->join(Board::TABLE.' as dst', 'id', 'dst_column_id', self::TABLE, 'dst')
                        ->findAll();
    }

    /**
     * Get project export
     *
     * @access public
     * @param  integer    $project_id      Project id
     * @param  mixed      $from            Start date (timestamp or user formatted date)
     * @param  mixed      $to              End date (timestamp or user formatted date)
     * @return array
     */
    public function export($project_id, $from, $to)
    {
        $results = array($this->getColumns());
        $transitions = $this->getAllByProjectAndDate($project_id, $from, $to);

        foreach ($transitions as $transition) {
            $results[] = $this->format($transition);
        }

        return $results;
    }

    /**
     * Get column titles
     *
     * @access public
     * @return string[]
     */
    public function getColumns()
    {
        return array(
            e('Id'),
            e('Task Title'),
            e('Source column'),
            e('Destination column'),
            e('Executer'),
            e('Date'),
            e('Time spent'),
        );
    }

    /**
     * Format the output of a transition array
     *
     * @access public
     * @param  array     $transition
     * @return array
     */
    public function format(array $transition)
    {
        $values = array();
        $values[] = $transition['id'];
        $values[] = $transition['title'];
        $values[] = $transition['src_column'];
        $values[] = $transition['dst_column'];
        $values[] = $transition['name'] ?: $transition['username'];
        $values[] = date('Y-m-d H:i', $transition['date']);
        $values[] = round($transition['time_spent'] / 3600, 2);

        return $values;
    }
}
