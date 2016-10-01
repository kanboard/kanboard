<?php

namespace Kanboard\Api\Authorization;

/**
 * Class ActionAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class ActionAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $action_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $this->actionModel->getProjectId($action_id));
        }
    }
}
