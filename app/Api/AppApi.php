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
        return $this->timezone->getCurrentTimezone();
    }

    public function getVersion()
    {
        return APP_VERSION;
    }

    public function getDefaultTaskColor()
    {
        return $this->color->getDefaultColor();
    }

    public function getDefaultTaskColors()
    {
        return $this->color->getDefaultColors();
    }

    public function getColorList()
    {
        return $this->color->getList();
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
