<?php

namespace Kanboard\Api\Authorization;

/**
 * Class TaskFileAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class TaskFileAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $file_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $this->taskFileModel->getProjectId($file_id));
        }
    }
}
