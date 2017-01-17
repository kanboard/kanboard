<?php

namespace Kanboard\Core\Plugin;

use Kanboard\Core\Base as BaseCore;

/**
 * Class Directory
 *
 * @package Kanboard\Core\Plugin
 * @author  Frederic Guillot
 */
class Directory extends BaseCore
{
    /**
     * Get all plugins available
     *
     * @access public
     * @param  string $url
     * @return array
     */
    public function getAvailablePlugins($url = PLUGIN_API_URL)
    {
        $plugins = $this->httpClient->getJson($url);
        $plugins = array_filter($plugins, array($this, 'isCompatible'));
        $plugins = array_filter($plugins, array($this, 'isInstallable'));
        return $plugins;
    }

    /**
     * Filter plugins
     *
     * @param array  $plugin
     * @param string $appVersion
     * @return bool
     */
    public function isCompatible(array $plugin, $appVersion = APP_VERSION)
    {
        return Version::isCompatible($plugin['compatible_version'], $appVersion);
    }

    /**
     * Filter plugins
     *
     * @param array  $plugin
     * @return bool
     */
    public function isInstallable(array $plugin)
    {
        return $plugin['remote_install'];
    }
}
