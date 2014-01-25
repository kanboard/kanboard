<?php

class Router
{
    private $controller = '';
    private $action = '';

    public function __construct($controller = '', $action = '')
    {
        $this->controller = empty($_GET['controller']) ? $controller : $_GET['controller'];
        $this->action = empty($_GET['action']) ? $controller : $_GET['action'];
    }

    public function sanitize($value, $default_value)
    {
        return ! ctype_alpha($value) || empty($value) ? $default_value : strtolower($value);
    }

    public function loadController($filename, $class, $method)
    {
        if (file_exists($filename)) {

            require $filename;

            if (! method_exists($class, $method)) return false;

            $instance = new $class;
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

        if (! $this->loadController('controllers/'.$this->controller.'.php', '\Controller\\'.$this->controller, $this->action)) {
            die('Page not found!');
        }
    }
}
