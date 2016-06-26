<?php

namespace Kanboard\Api\Procedure;

/**
 * App API controller
 *
 * @package  Kanboard\Api\Procedure
 * @author   Frederic Guillot
 */
class AppProcedure extends BaseProcedure
{
    public function getTimezone()
    {
        return $this->timezoneModel->getCurrentTimezone();
    }

    public function getVersion()
    {
        return APP_VERSION;
    }

    public function getDefaultTaskColor()
    {
        return $this->colorModel->getDefaultColor();
    }

    public function getDefaultTaskColors()
    {
        return $this->colorModel->getDefaultColors();
    }

    public function getColorList()
    {
        return $this->colorModel->getList();
    }

    public function getApplicationRoles()
    {
        return $this->role->getApplicationRoles();
    }

    public function getProjectRoles()
    {
        return $this->role->getProjectRoles();
    }
}
