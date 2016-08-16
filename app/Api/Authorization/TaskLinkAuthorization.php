<?php

namespace Kanboard\Api\Authorization;

/**
 * Class TaskLinkAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class TaskLinkAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $task_link_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $this->taskLinkModel->getProjectId($task_link_id));
        }
    }
}
