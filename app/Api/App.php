<?php

namespace Kanboard\Api;

/**
 * App API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class App extends \Kanboard\Core\Base
{
    public function getTimezone()
    {
        return $this->config->get('application_timezone');
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
}
