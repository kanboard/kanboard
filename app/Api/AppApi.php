<?php

namespace Kanboard\Api;

use Kanboard\Core\Base;

/**
 * App API controller
 *
 * @package  Kanboard\Api
 * @author   Frederic Guillot
 */
class AppApi extends Base
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
