<?php

namespace Api;

/**
 * App API controller
 *
 * @package  api
 * @author   Frederic Guillot
 */
class App extends Base
{
    public function getTimezone()
    {
        return $this->config->get('application_timezone');
    }

    public function getVersion()
    {
        return APP_VERSION;
    }
}
