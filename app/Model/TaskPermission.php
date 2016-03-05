<?php

namespace Kanboard\Model;

use Kanboard\Core\Security\Role;

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
     * @param  array $task
     * @return bool
     */
    public function canRemoveTask(array $task)
    {
        if ($this->userSession->isAdmin() || $this->projectUserRole->getUserRole($task['project_id'], $this->userSession->getId()) === Role::PROJECT_MANAGER) {
            return true;
        } elseif (isset($task['creator_id']) && $task['creator_id'] == $this->userSession->getId()) {
            return true;
        }

        return false;
    }
}
