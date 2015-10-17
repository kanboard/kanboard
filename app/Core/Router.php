<?php

namespace Kanboard\Core;

use RuntimeException;

/**
 * Router class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Router extends Base
{
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
     * Store routes for path lookup
     *
     * @access private
     * @var array
     */
    private $paths = array();

    /**
     * Store routes for url lookup
     *
     * @access private
     * @var array
     */
    private $urls = array();

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
     * Get the path to compare patterns
     *
     * @access public
     * @param  string  $uri
     * @param  string  $query_string
     * @return string
     */
    public function getPath($uri, $query_string = '')
    {
        $path = substr($uri, strlen($this->helper->url->dir()));

        if (! empty($query_string)) {
            $path = substr($path, 0, - strlen($query_string) - 1);
        }

        if (! empty($path) && $path{0} === '/') {
            $path = substr($path, 1);
        }

        return $path;
    }

    /**
     * Add route
     *
     * @access public
     * @param  string   $path
     * @param  string   $controller
     * @param  string   $action
     * @param  array    $params
     */
    public function addRoute($path, $controller, $action, array $params = array())
    {
        $pattern = explode('/', $path);

        $this->paths[] = array(
            'pattern' => $pattern,
            'count' => count($pattern),
            'controller' => $controller,
            'action' => $action,
        );

        $this->urls[$controller][$action][] = array(
            'path' => $path,
            'params' => array_flip($params),
            'count' => count($params),
        );
    }

    /**
     * Find a route according to the given path
     *
     * @access public
     * @param  string   $path
     * @return array
     */
    public function findRoute($path)
    {
        $parts = explode('/', $path);
        $count = count($parts);

        foreach ($this->paths as $route) {
            if ($count === $route['count']) {
                $params = array();

                for ($i = 0; $i < $count; $i++) {
                    if ($route['pattern'][$i]{0} === ':') {
                        $params[substr($route['pattern'][$i], 1)] = $parts[$i];
                    } elseif ($route['pattern'][$i] !== $parts[$i]) {
                        break;
                    }
                }

                if ($i === $count) {
                    $_GET = array_merge($_GET, $params);
                    return array($route['controller'], $route['action']);
                }
            }
        }

        return array('app', 'index');
    }

    /**
     * Find route url
     *
     * @access public
     * @param  string   $controller
     * @param  string   $action
     * @param  array    $params
     * @return string
     */
    public function findUrl($controller, $action, array $params = array())
    {
        if (! isset($this->urls[$controller][$action])) {
            return '';
        }

        foreach ($this->urls[$controller][$action] as $pattern) {
            if (array_diff_key($params, $pattern['params']) === array()) {
                $url = $pattern['path'];
                $i = 0;

                foreach ($params as $variable => $value) {
                    $url = str_replace(':'.$variable, $value, $url);
                    $i++;
                }

                if ($i === $pattern['count']) {
                    return $url;
                }
            }
        }

        return '';
    }

    /**
     * Check controller and action parameter
     *
     * @access public
     * @param  string $value Controller or action name
     * @param  string $default_value Default value if validation fail
     * @return string
     */
    public function sanitize($value, $default_value)
    {
        return ! preg_match('/^[a-zA-Z_0-9]+$/', $value) ? $default_value : $value;
    }

    /**
     * Find controller/action from the route table or from get arguments
     *
     * @access public
     * @param  string  $uri
     * @param  string  $query_string
     */
    public function dispatch($uri, $query_string = '')
    {
        if (! empty($_GET['controller']) && ! empty($_GET['action'])) {
            $this->controller = $this->sanitize($_GET['controller'], 'app');
            $this->action = $this->sanitize($_GET['action'], 'index');
            $plugin = ! empty($_GET['plugin']) ? $this->sanitize($_GET['plugin'], '') : '';
        } else {
            list($this->controller, $this->action) = $this->findRoute($this->getPath($uri, $query_string)); // TODO: add plugin for routes
            $plugin = '';
        }

        $class = '\Kanboard\\';
        $class .= empty($plugin) ? 'Controller\\'.ucfirst($this->controller) : 'Plugin\\'.ucfirst($plugin).'\Controller\\'.ucfirst($this->controller);

        if (! class_exists($class) || ! method_exists($class, $this->action)) {
            throw new RuntimeException('Controller or method not found for the given url!');
        }

        $instance = new $class($this->container);
        $instance->beforeAction($this->controller, $this->action);
        $instance->{$this->action}();
    }
}
