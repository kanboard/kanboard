<?php

namespace Core;

class Request
{
    public function getStringParam($name, $default_value = '')
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default_value;
    }

    public function getIntegerParam($name, $default_value = 0)
    {
        return isset($_GET[$name]) && ctype_digit($_GET[$name]) ? (int) $_GET[$name] : $default_value;
    }

    public function getValue($name)
    {
        $values = $this->getValues();
        return isset($values[$name]) ? $values[$name] : null;
    }

    public function getValues()
    {
        if (! empty($_POST)) return $_POST;

        $result = json_decode($this->getBody(), true);
        if ($result) return $result;

        return array();
    }

    public function getBody()
    {
        return file_get_contents('php://input');
    }

    public function getFileContent($name)
    {
        if (isset($_FILES[$name])) {
            return file_get_contents($_FILES[$name]['tmp_name']);
        }

        return '';
    }

    public function isPost()
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
