<?php

namespace Model;

/**
 * Task permission model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class TaskPermission extends Base
{
    /**
     * Return true if the user can remove a task
     *
     * Regular users can't remove tasks from other people
     *
     * @public
     * @return boolean
     */
    public function canRemoveTask(array $task)
    {
        if ($this->userSession->isAdmin()) {
            return true;
        }
        else if (isset($task['creator_id']) && $task['creator_id'] == $this->userSession->getId()) {
            return true;
        }

        return false;
    }
}
