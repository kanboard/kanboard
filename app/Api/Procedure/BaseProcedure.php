<?php

namespace Kanboard\Api\Procedure;

use Kanboard\Api\Authorization\ProcedureAuthorization;
use Kanboard\Api\Authorization\UserAuthorization;
use Kanboard\Core\Base;
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

    protected function getClassName()
    {
        $reflection = new ReflectionClass(get_called_class());
        return $reflection->getShortName();
    }
}
