<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\TaskAuthorization;
use Kanboard\Api\Authorization\TaskLinkAuthorization;

/**
 * TaskLink API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class TaskLinkProcedure extends BaseProcedure
{
    /**
     * Get a task link
     *
     * @access public
     * @param  integer   $task_link_id   Task link id
     * @return array
     */
    public function getTaskLinkById($task_link_id)
    {
        TaskLinkAuthorization::getInstance($this->container)->check($this->getClassName(), 'getTaskLinkById', $task_link_id);
        return $this->taskLinkModel->getById($task_link_id);
    }

    /**
     * Get all links attached to a task
     *
     * @access public
     * @param  integer   $task_id   Task id
     * @return array
     */
    public function getAllTaskLinks($task_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'getAllTaskLinks', $task_id);
        return $this->taskLinkModel->getAll($task_id);
    }

    /**
     * Create a new link
     *
     * @access public
     * @param  integer   $task_id            Task id
     * @param  integer   $opposite_task_id   Opposite task id
     * @param  integer   $link_id            Link id
     * @return integer                       Task link id
     */
    public function createTaskLink($task_id, $opposite_task_id, $link_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'createTaskLink', $task_id);

        if ($this->userSession->isLogged()) {
            $opposite_task = $this->taskFinderModel->getById($opposite_task_id);

            if (! $this->projectPermissionModel->isUserAllowed($opposite_task['project_id'], $this->userSession->getId())) {
                return false;
            }
        }

        return $this->taskLinkModel->create($task_id, $opposite_task_id, $link_id);
    }

    /**
     * Update a task link
     *
     * @access public
     * @param  integer   $task_link_id          Task link id
     * @param  integer   $task_id               Task id
     * @param  integer   $opposite_task_id      Opposite task id
     * @param  integer   $link_id               Link id
     * @return boolean
     */
    public function updateTaskLink($task_link_id, $task_id, $opposite_task_id, $link_id)
    {
        TaskAuthorization::getInstance($this->container)->check($this->getClassName(), 'updateTaskLink', $task_id);

        if ($this->userSession->isLogged()) {
            $opposite_task = $this->taskFinderModel->getById($opposite_task_id);

            if (! $this->projectPermissionModel->isUserAllowed($opposite_task['project_id'], $this->userSession->getId())) {
                return false;
            }
        }

        return $this->taskLinkModel->update($task_link_id, $task_id, $opposite_task_id, $link_id);
    }

    /**
     * Remove a link between two tasks
     *
     * @access public
     * @param  integer   $task_link_id
     * @return boolean
     */
    public function removeTaskLink($task_link_id)
    {
        TaskLinkAuthorization::getInstance($this->container)->check($this->getClassName(), 'removeTaskLink', $task_link_id);
        return $this->taskLinkModel->remove($task_link_id);
    }
}
