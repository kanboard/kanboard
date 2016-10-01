<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class SubtaskPositionModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class SubtaskPositionModel extends Base
{
    /**
     * Change subtask position
     *
     * @access public
     * @param  integer  $task_id
     * @param  integer  $subtask_id
     * @param  integer  $position
     * @return boolean
     */
    public function changePosition($task_id, $subtask_id, $position)
    {
        if ($position < 1 || $position > $this->db->table(SubtaskModel::TABLE)->eq('task_id', $task_id)->count()) {
            return false;
        }

        $subtask_ids = $this->db->table(SubtaskModel::TABLE)->eq('task_id', $task_id)->neq('id', $subtask_id)->asc('position')->findAllByColumn('id');
        $offset = 1;
        $results = array();

        foreach ($subtask_ids as $current_subtask_id) {
            if ($offset == $position) {
                $offset++;
            }

            $results[] = $this->db->table(SubtaskModel::TABLE)->eq('id', $current_subtask_id)->update(array('position' => $offset));
            $offset++;
        }

        $results[] = $this->db->table(SubtaskModel::TABLE)->eq('id', $subtask_id)->update(array('position' => $position));

        return !in_array(false, $results, true);
    }
}
