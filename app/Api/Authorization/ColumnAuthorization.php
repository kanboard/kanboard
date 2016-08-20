<?php

namespace Kanboard\Api\Authorization;

/**
 * Class ColumnAuthorization
 *
 * @package Kanboard\Api\Authorization
 * @author  Frederic Guillot
 */
class ColumnAuthorization extends ProjectAuthorization
{
    public function check($class, $method, $column_id)
    {
        if ($this->userSession->isLogged()) {
            $this->checkProjectPermission($class, $method, $this->columnModel->getProjectId($column_id));
        }
    }
}
