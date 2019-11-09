<?php

namespace Kanboard\Core\Http;

use Kanboard\Core\Base;

/**
 * Route Dispatcher
 *
 * @package http
 * @author  Frederic Guillot
 */
class Router extends Base
{
    const DEFAULT_CONTROLLER = 'DashboardController';
    const DEFAULT_METHOD = 'show';

    /**
     * Plugin name
     *
     * @access private
     * @var string
     */
    private $currentPluginName = '';

    /**
     * Controller
     *
     * @access private
     * @var string
     */
    private $currentControllerName = '';

    /**
     * Action
     *
     * @access private
     * @var string
     */
    private $currentActionName = '';

    /**
     * Get plugin name
     *
     * @access public
     * @return string
     */
    public function getPlugin()
    {
        return $this->currentPluginName;
    }

    /**
     * Get controller
     *
     * @access public
     * @return string
     */
    public function getController()
    {
        return $this->currentControllerName;
    }

    /**
     * Get action
     *
     * @access public
     * @return string
     */
    public function getAction()
    {
        return $this->currentActionName;
    }

    /**
     * Get the path to compare patterns
     *
     * @access public
     * @return string
     */
    public function getPath()
    {
        $path = substr($this->request->getUri(), strlen($this->helper->url->dir()));

        if ($this->request->getQueryString() !== '') {
            $path = substr($path, 0, - strlen($this->request->getQueryString()) - 1);
        }

        if ($path !== '' && $path[0] === '/') {
            $path = substr($path, 1);
        }

        return $path;
    }

    /**
     * Find controller/action from the route table or from get arguments
     *
     * @access public
     */
    public function dispatch()
    {
        $controller = $this->request->getStringParam('controller');
        $action = $this->request->getStringParam('action');
        $plugin = $this->request->getStringParam('plugin');

        if ($controller === '') {
            $route = $this->route->findRoute($this->getPath());
            $controller = $route['controller'];
            $action = $route['action'];
            $plugin = $route['plugin'];
        }

        $this->currentControllerName = ucfirst($this->sanitize($controller, self::DEFAULT_CONTROLLER));
        $this->currentActionName = $this->sanitize($action, self::DEFAULT_METHOD);
        $this->currentPluginName = ucfirst($this->sanitize($plugin));
    }

    /**
     * Check controller and action parameter
     *
     * @access public
     * @param  string $value
     * @param  string $default
     * @return string
     */
    public function sanitize($value, $default = '')
    {
        return preg_match('/^[a-zA-Z_0-9]+$/', $value) ? $value : $default;
    }
}
