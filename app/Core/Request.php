<?php

namespace Core;

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
     * Get form values and check for CSRF token
     *
     * @access public
     * @return array
     */
    public function getValues()
    {
        if (! empty($_POST) && Security::validateCSRFFormToken($_POST)) {
            return $_POST;
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

    /**
     * Returns current request's query string, useful for redirecting
     *
     * @access public
     * @return string
     */
    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    /**
     * Get the user agent
     *
     * @static
     * @access public
     * @return string
     */
    public static function getUserAgent()
    {
        return empty($_SERVER['HTTP_USER_AGENT']) ? t('Unknown') : $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Get the real IP address of the user
     *
     * @static
     * @access public
     * @param  bool    $only_public   Return only public IP address
     * @return string
     */
    public static function getIpAddress($only_public = false)
    {
        $keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );

        foreach ($keys as $key) {

            if (isset($_SERVER[$key])) {

                foreach (explode(',', $_SERVER[$key]) as $ip_address) {

                    $ip_address = trim($ip_address);

                    if ($only_public) {

                        // Return only public IP address
                        if (filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                            return $ip_address;
                        }
                    }
                    else {

                        return $ip_address;
                    }
                }
            }
        }

        return t('Unknown');
    }
}
