<?php

namespace Kanboard\Api\Authorization;

/**
 * Class TaskAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class TaskAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $task_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $this->taskFinderModel->getProjectId($task_id));
        }
    }
}
