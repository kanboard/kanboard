<?php

namespace Core;

use Pimple\Container;

/**
 * Router class
 *
 * @package core
 * @author  Frederic Guillot
 */
class Router
{
    /**
     * Controller name
     *
     * @access private
     * @var string
     */
    private $controller = '';

    /**
     * Action name
     *
     * @access private
     * @var string
     */
    private $action = '';

    /**
     * Container instance
     *
     * @access private
     * @var \Pimple\Container
     */
    private $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container     Container instance
     * @param  string              $controller    Controller name
     * @param  string              $action        Action name
     */
    public function __construct(Container $container, $controller = '', $action = '')
    {
        $this->container = $container;
        $this->controller = empty($_GET['controller']) ? $controller : $_GET['controller'];
        $this->action = empty($_GET['action']) ? $action : $_GET['action'];
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
        return ! ctype_alpha($value) || empty($value) ? $default_value : strtolower($value);
    }

    /**
     * Load a controller and execute the action
     *
     * @access public
     * @param  string $filename Controller filename
     * @param  string $class Class name
     * @param  string $method Method name
     * @return bool
     */
    public function load($filename, $class, $method)
    {
        if (file_exists($filename)) {

            require $filename;

            if (! method_exists($class, $method)) {
                return false;
            }

            $instance = new $class($this->container);
            $instance->beforeAction($this->controller, $this->action);
            $instance->$method();

            return true;
        }

        return false;
    }

    /**
     * Find a route
     *
     * @access public
     */
    public function execute()
    {
        $this->controller = $this->sanitize($this->controller, 'app');
        $this->action = $this->sanitize($this->action, 'index');
        $filename = __DIR__.'/../Controller/'.ucfirst($this->controller).'.php';

        if (! $this->load($filename, '\Controller\\'.$this->controller, $this->action)) {
            die('Page not found!');
        }
    }
}
