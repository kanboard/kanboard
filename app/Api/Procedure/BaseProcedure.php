<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProcedureAuthorization;
use Kanboard\Api\Authorization\UserAuthorization;
use Kanboard\Core\Base;
use Kanboard\Core\Security\Role;
use ReflectionClass;

/**
 * Base class
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
abstract class BaseProcedure extends Base
{
    public function beforeProcedure($procedure)
    {
        ProcedureAuthorization::getInstance($this->container)->check($procedure);
        UserAuthorization::getInstance($this->container)->check($this->getClassName(), $procedure);
    }

    protected function filterValues(array $values)
    {
        foreach ($values as $key => $value) {
            if (is_null($value)) {
                unset($values[$key]);
            }
        }

        return $values;
    }

    protected function filterUser($user)
    {
        return is_array($user) ? $this->userModel->removePrivateColumns($user) : $user;
    }

    protected function filterUsers($users)
    {
        return is_array($users) ? array_map(array($this, 'filterUser'), $users) : $users;
    }

    /**
     * Role to use when filtering activity events for the current API caller
     *
     * The application token ("jsonrpc") runs without a user session and is not
     * subject to per-user authorization, so it is treated as an administrator
     * to stay consistent with the other comment endpoints.
     *
     * @access protected
     * @return string
     */
    protected function getActivityRole()
    {
        if ($this->userSession->isLogged()) {
            return $this->userSession->getRole();
        }

        return Role::APP_ADMIN;
    }

    protected function getClassName()
    {
        $reflection = new ReflectionClass(get_called_class());
        return $reflection->getShortName();
    }
}
