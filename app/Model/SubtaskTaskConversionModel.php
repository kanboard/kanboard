<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class SubtaskTaskConversionModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class SubtaskTaskConversionModel extends Base
{
    /**
     * Convert a subtask to a task
     *
     * @access public
     * @param  integer $project_id
     * @param  integer $subtask_id
     * @return integer
     */
    public function convertToTask($project_id, $subtask_id)
    {
        $subtask = $this->subtaskModel->getById($subtask_id);

        $task_id = $this->taskCreationModel->create(array(
            'project_id' => $project_id,
            'title' => $subtask['title'],
            'time_estimated' => $subtask['time_estimated'],
            'time_spent' => $subtask['time_spent'],
            'owner_id' => $subtask['user_id'],
        ));

        if ($task_id !== false) {
            $this->subtaskModel->remove($subtask_id);
        }

        return $task_id;
    }
}
