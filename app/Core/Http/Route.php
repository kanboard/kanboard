<?php

namespace Kanboard\Core\Http;

use Kanboard\Core\Base;

/**
 * Route Handler
 *
 * @package http
 * @author  Frederic Guillot
 */
class Route extends Base
{
    /**
     * Flag that enable the routing table
     *
     * @access private
     * @var boolean
     */
    private $activated = false;

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
     * Enable routing table
     *
     * @access public
     * @return Route
     */
    public function enable()
    {
        $this->activated = true;
        return $this;
    }

    /**
     * Add route
     *
     * @access public
     * @param  string   $path
     * @param  string   $controller
     * @param  string   $action
     * @param  string   $plugin
     * @return Route
     */
    public function addRoute($path, $controller, $action, $plugin = '')
    {
        if ($this->activated) {
            $path = ltrim($path, '/');
            $items = explode('/', $path);
            $params = $this->findParams($items);

            $this->paths[] = array(
                'items' => $items,
                'count' => count($items),
                'controller' => $controller,
                'action' => $action,
                'plugin' => $plugin,
            );

            $this->urls[$plugin][$controller][$action][] = array(
                'path' => $path,
                'params' => $params,
                'count' => count($params),
            );
        }

        return $this;
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
        $items = explode('/', ltrim($path, '/'));
        $count = count($items);

        foreach ($this->paths as $route) {
            if ($count === $route['count']) {
                $params = array();

                for ($i = 0; $i < $count; $i++) {
                    if ($route['items'][$i][0] === ':') {
                        $params[substr($route['items'][$i], 1)] = $items[$i];
                    } elseif ($route['items'][$i] !== $items[$i]) {
                        break;
                    }
                }

                if ($i === $count) {
                    $this->request->setParams($params);
                    return array(
                        'controller' => $route['controller'],
                        'action' => $route['action'],
                        'plugin' => $route['plugin'],
                    );
                }
            }
        }

        return array(
            'controller' => 'DashboardController',
            'action' => 'show',
            'plugin' => '',
        );
    }

    /**
     * Find route url
     *
     * @access public
     * @param  string   $controller
     * @param  string   $action
     * @param  array    $params
     * @param  string   $plugin
     * @return string
     */
    public function findUrl($controller, $action, array $params = array(), $plugin = '')
    {
        if ($plugin === '' && isset($params['plugin'])) {
            $plugin = $params['plugin'];
            unset($params['plugin']);
        }

        if (! isset($this->urls[$plugin][$controller][$action])) {
            return '';
        }

        foreach ($this->urls[$plugin][$controller][$action] as $route) {
            if (array_diff_key($params, $route['params']) === array()) {
                $url = $route['path'];
                $i = 0;

                foreach ($params as $variable => $value) {
                    $url = str_replace(':'.$variable, $value, $url);
                    $i++;
                }

                if ($i === $route['count']) {
                    return $url;
                }
            }
        }

        return '';
    }

    /**
     * Find url params
     *
     * @access public
     * @param  array $items
     * @return array
     */
    public function findParams(array $items)
    {
        $params = array();

        foreach ($items as $item) {
            if ($item !== '' && $item[0] === ':') {
                $params[substr($item, 1)] = true;
            }
        }

        return $params;
    }
}
