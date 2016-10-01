<?php

namespace Kanboard\Api\Authorization;

/**
 * Class SubtaskAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class SubtaskAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $subtask_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $this->subtaskModel->getProjectId($subtask_id));
        }
    }
}
