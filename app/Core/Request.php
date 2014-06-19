<?php

namespace Core;

use Core\Security;

/**
 * Request class
 *
 * @package  core
 * @author   Frederic Guillot
 */
class Request
{
    /**
     * Get URL string parameter
     *
     * @access public
     * @param  string   $name            Parameter name
     * @param  string   $default_value   Default value
     * @return string
     */
    public function getStringParam($name, $default_value = '')
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default_value;
    }

    /**
     * Get URL integer parameter
     *
     * @access public
     * @param  string   $name            Parameter name
     * @param  integer  $default_value   Default value
     * @return integer
     */
    public function getIntegerParam($name, $default_value = 0)
    {
        return isset($_GET[$name]) && ctype_digit($_GET[$name]) ? (int) $_GET[$name] : $default_value;
    }

    /**
     * Get a form value
     *
     * @access public
     * @param  string    $name   Form field name
     * @return string|null
     */
    public function getValue($name)
    {
        $values = $this->getValues();
        return isset($values[$name]) ? $values[$name] : null;
    }

    /**
     * Get form values or unserialized json request
     *
     * @access public
     * @return array
     */
    public function getValues()
    {
        if (! empty($_POST)) {

            if (Security::validateCSRFFormToken($_POST)) {
                return $_POST;
            }

            return array();
        }

        $result = json_decode($this->getBody(), true);

        if ($result) {
            return $result;
        }

        return array();
    }

    /**
     * Get the raw body of the HTTP request
     *
     * @access public
     * @return string
     */
    public function getBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * Get the content of an uploaded file
     *
     * @access public
     * @param  string   $name   Form file name
     * @return string
     */
    public function getFileContent($name)
    {
        if (isset($_FILES[$name])) {
            return file_get_contents($_FILES[$name]['tmp_name']);
        }

        return '';
    }

    /**
     * Return true if the HTTP request is sent with the POST method
     *
     * @access public
     * @return bool
     */
    public function isPost()
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Return true if the HTTP request is an Ajax request
     *
     * @access public
     * @return bool
     */
    public function isAjax()
    {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Return a HTTP header value
     *
     * @access public
     * @param  string   $name   Header name
     * @return string
     */
    public function getHeader($name)
    {
        $name = 'HTTP_'.str_replace('-', '_', strtoupper($name));
        return isset($_SERVER[$name]) ? $_SERVER[$name] : '';
    }
}
