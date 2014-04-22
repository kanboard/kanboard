<?php

namespace Core;

require __DIR__.'/request.php';
require __DIR__.'/response.php';
require __DIR__.'/session.php';
require __DIR__.'/template.php';

class Router
{
    private $controller = '';
    private $action = '';
    private $registry;

    public function __construct(Registry $registry, $controller = '', $action = '')
    {
        $this->registry = $registry;
        $this->controller = empty($_GET['controller']) ? $controller : $_GET['controller'];
        $this->action = empty($_GET['action']) ? $controller : $_GET['action'];
    }

    /**
     * @param string $default_value
     */
    public function sanitize($value, $default_value)
    {
        return ! ctype_alpha($value) || empty($value) ? $default_value : strtolower($value);
    }

    /**
     * @param string $filename
     * @param string $class
     */
    public function load($filename, $class, $method)
    {
        if (file_exists($filename)) {

            require $filename;

            if (! method_exists($class, $method)) return false;

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

    public function execute()
    {
        $this->controller = $this->sanitize($this->controller, 'app');
        $this->action = $this->sanitize($this->action, 'index');

        if (! $this->load('controllers/'.$this->controller.'.php', '\Controller\\'.$this->controller, $this->action)) {
            die('Page not found!');
        }
    }
}
