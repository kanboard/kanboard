<?php

namespace PicoDb;

/**
 * Class UrlParser
 *
 * @package PicoDb
 * @author  Frederic Guillot
 */
class UrlParser
{
    /**
     * URL
     *
     * @access private
     * @var string
     */
    private $url;

    /**
     * Constructor
     *
     * @access public
     * @param  string $environmentVariable
     */
    public function __construct($environmentVariable = 'DATABASE_URL')
    {
        $this->url = getenv($environmentVariable);
    }

    /**
     * Get object instance
     *
     * @access public
     * @param  string $environmentVariable
     * @return static
     */
    public static function getInstance($environmentVariable = 'DATABASE_URL')
    {
        return new static($environmentVariable);
    }

    /**
     * Return true if the variable is defined
     *
     * @access public
     * @return bool
     */
    public function isEnvironmentVariableDefined()
    {
        return ! empty($this->url);
    }

    /**
     * Get settings from URL
     *
     * @access public
     * @param  string $url
     * @return array
     */
    public function getSettings($url = '')
    {
        $url = $url ?: $this->url;
        $components = parse_url($url);

        if ($components === false) {
            return array();
        }

        return array(
            'driver' => $this->getUrlComponent($components, 'scheme'),
            'username' => $this->getUrlComponent($components, 'user'),
            'password' => $this->getUrlComponent($components, 'pass'),
            'hostname' => $this->getUrlComponent($components, 'host'),
            'port' => $this->getUrlComponent($components, 'port'),
            'database' => ltrim($this->getUrlComponent($components, 'path'), '/'),
        );
    }

    /**
     * Get URL component
     *
     * @access private
     * @param  array  $components
     * @param  string $component
     * @return mixed|null
     */
    private function getUrlComponent(array $components, $component)
    {
        return ! empty($components[$component]) ? $components[$component] : null;
    }
}
