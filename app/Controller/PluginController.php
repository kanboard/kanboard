<?php

namespace Kanboard\Controller;

/**
 * Class PluginController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class PluginController extends BaseController
{
    /**
     * Display the plugin page
     *
     * @access public
     */
    public function show()
    {
        $this->response->html($this->helper->layout->plugin('plugin/show', array(
            'plugins' => $this->pluginLoader->plugins,
            'title' => t('Installed Plugins'),
        )));
    }

    /**
     * Display list of available plugins
     */
    public function directory()
    {
        $plugins = $this->httpClient->getJson(PLUGIN_API_URL);

        $this->response->html($this->helper->layout->plugin('plugin/directory', array(
            'plugins' => $plugins,
            'title' => t('Plugin Directory'),
        )));
    }
}
