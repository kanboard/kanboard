<?php

namespace Core;

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
     * Registry instance
     *
     * @access private
     * @var Core\Registry
     */
    private $registry;

    /**
     * Constructor
     *
     * @access public
     * @param  Registry        $registry     Registry instance
     * @param  string          $controller   Controller name
     * @param  string          $action       Action name
     */
    public function __construct(Registry $registry, $controller = '', $action = '')
    {
        $this->registry = $registry;
        $this->controller = empty($_GET['controller']) ? $controller : $_GET['controller'];
        $this->action = empty($_GET['action']) ? $action : $_GET['action'];
    }

    /**
     * Check controller and action parameter
     *
     * @access public
     * @param  string    $value           Controller or action name
     * @param  string    $default_value   Default value if validation fail
     */
    public function sanitize($value, $default_value)
    {
        return ! ctype_alpha($value) || empty($value) ? $default_value : strtolower($value);
    }

    /**
     * Load a controller and execute the action
     *
     * @access public
     * @param  string     $filename     Controller filename
     * @param  string     $class        Class name
     * @param  string     $method       Method name
     */
    public function load($filename, $class, $method)
    {
        if (file_exists($filename)) {

            require $filename;

            if (! method_exists($class, $method)) {
                return false;
            }

            $instance = new $class($this->registry);
            $instance->request = new Request;
            $instance->response = new Response;
            $instance->session = new Session;
            $instance->template = new Template;
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
