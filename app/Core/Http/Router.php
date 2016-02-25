<?php

namespace Kanboard\Core\Http;

use RuntimeException;
use Kanboard\Core\Base;

/**
 * Route Dispatcher
 *
 * @package http
 * @author  Frederic Guillot
 */
class Router extends Base
{
    /**
     * Plugin name
     *
     * @access private
     * @var string
     */
    private $plugin = '';

    /**
     * Controller
     *
     * @access private
     * @var string
     */
    private $controller = '';

    /**
     * Action
     *
     * @access private
     * @var string
     */
    private $action = '';

    /**
     * Get plugin name
     *
     * @access public
     * @return string
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Get controller
     *
     * @access public
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get action
     *
     * @access public
     * @return string
     */
    public function getAction()
    {
        return $this->action;
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

        if ($path !== '' && $path{0} === '/') {
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

        $this->controller = ucfirst($this->sanitize($controller, 'app'));
        $this->action = $this->sanitize($action, 'index');
        $this->plugin = ucfirst($this->sanitize($plugin));

        return $this->executeAction();
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

    /**
     * Execute controller action
     *
     * @access private
     */
    private function executeAction()
    {
        $class = $this->getControllerClassName();

        if (! class_exists($class)) {
            throw new RuntimeException('Controller not found');
        }

        if (! method_exists($class, $this->action)) {
            throw new RuntimeException('Action not implemented');
        }

        $instance = new $class($this->container);
        $instance->beforeAction();
        $instance->{$this->action}();
        return $instance;
    }

    /**
     * Get controller class name
     *
     * @access private
     * @return string
     */
    private function getControllerClassName()
    {
        if ($this->plugin !== '') {
            return '\Kanboard\Plugin\\'.$this->plugin.'\Controller\\'.$this->controller;
        }

        return '\Kanboard\Controller\\'.$this->controller;
    }
}
