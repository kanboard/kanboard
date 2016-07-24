<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Class SubtaskStatusModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class SubtaskStatusModel extends Base
{
    /**
     * Get the subtask in progress for this user
     *
     * @access public
     * @param  integer   $user_id
     * @return array
     */
    public function getSubtaskInProgress($user_id)
    {
        return $this->db->table(SubtaskModel::TABLE)
            ->eq('status', SubtaskModel::STATUS_INPROGRESS)
            ->eq('user_id', $user_id)
            ->findOne();
    }

    /**
     * Return true if the user have a subtask in progress
     *
     * @access public
     * @param  integer   $user_id
     * @return boolean
     */
    public function hasSubtaskInProgress($user_id)
    {
        return $this->configModel->get('subtask_restriction') == 1 &&
            $this->db->table(SubtaskModel::TABLE)
                ->eq('status', SubtaskModel::STATUS_INPROGRESS)
                ->eq('user_id', $user_id)
                ->exists();
    }

    /**
     * Change the status of subtask
     *
     * @access public
     * @param  integer  $subtask_id
     * @return boolean|integer
     */
    public function toggleStatus($subtask_id)
    {
        $subtask = $this->subtaskModel->getById($subtask_id);
        $status = ($subtask['status'] + 1) % 3;

        $values = array(
            'id' => $subtask['id'],
            'status' => $status,
            'task_id' => $subtask['task_id'],
        );

        if (empty($subtask['user_id']) && $this->userSession->isLogged()) {
            $values['user_id'] = $this->userSession->getId();
            $subtask['user_id'] = $values['user_id'];
        }

        $this->subtaskTimeTrackingModel->toggleTimer($subtask_id, $subtask['user_id'], $status);

        return $this->subtaskModel->update($values) ? $status : false;
    }

    /**
     * Close all subtasks of a task
     *
     * @access public
     * @param  integer  $task_id
     * @return boolean
     */
    public function closeAll($task_id)
    {
        return $this->db
            ->table(SubtaskModel::TABLE)
            ->eq('task_id', $task_id)
            ->update(array('status' => SubtaskModel::STATUS_DONE));
    }
}
