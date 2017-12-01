<?php

namespace PicoFeed\Config;

/**
 * Config class.
 *
 * @author  Frederic Guillot
 *
 * @method  \PicoFeed\Config\Config setClientTimeout(integer $value)
 * @method  \PicoFeed\Config\Config setClientUserAgent(string $value)
 * @method  \PicoFeed\Config\Config setMaxRedirections(integer $value)
 * @method  \PicoFeed\Config\Config setMaxBodySize(integer $value)
 * @method  \PicoFeed\Config\Config setProxyHostname(string $value)
 * @method  \PicoFeed\Config\Config setProxyPort(integer $value)
 * @method  \PicoFeed\Config\Config setProxyUsername(string $value)
 * @method  \PicoFeed\Config\Config setProxyPassword(string $value)
 * @method  \PicoFeed\Config\Config setGrabberRulesFolder(string $value)
 * @method  \PicoFeed\Config\Config setGrabberTimeout(integer $value)
 * @method  \PicoFeed\Config\Config setGrabberUserAgent(string $value)
 * @method  \PicoFeed\Config\Config setParserHashAlgo(string $value)
 * @method  \PicoFeed\Config\Config setContentFiltering(boolean $value)
 * @method  \PicoFeed\Config\Config setTimezone(string $value)
 * @method  \PicoFeed\Config\Config setFilterIframeWhitelist(array $value)
 * @method  \PicoFeed\Config\Config setFilterIntegerAttributes(array $value)
 * @method  \PicoFeed\Config\Config setFilterAttributeOverrides(array $value)
 * @method  \PicoFeed\Config\Config setFilterRequiredAttributes(array $value)
 * @method  \PicoFeed\Config\Config setFilterMediaBlacklist(array $value)
 * @method  \PicoFeed\Config\Config setFilterMediaAttributes(array $value)
 * @method  \PicoFeed\Config\Config setFilterSchemeWhitelist(array $value)
 * @method  \PicoFeed\Config\Config setFilterWhitelistedTags(array $value)
 * @method  \PicoFeed\Config\Config setFilterBlacklistedTags(array $value)
 * @method  \PicoFeed\Config\Config setFilterImageProxyUrl($value)
 * @method  \PicoFeed\Config\Config setFilterImageProxyCallback($closure)
 * @method  \PicoFeed\Config\Config setFilterImageProxyProtocol($value)
 * @method  integer    getClientTimeout()
 * @method  string     getClientUserAgent()
 * @method  integer    getMaxRedirections()
 * @method  integer    getMaxBodySize()
 * @method  string     getProxyHostname()
 * @method  integer    getProxyPort()
 * @method  string     getProxyUsername()
 * @method  string     getProxyPassword()
 * @method  string     getGrabberRulesFolder()
 * @method  integer    getGrabberTimeout()
 * @method  string     getGrabberUserAgent()
 * @method  string     getParserHashAlgo()
 * @method  boolean    getContentFiltering(bool $default_value)
 * @method  string     getTimezone()
 * @method  array      getFilterIframeWhitelist(array $default_value)
 * @method  array      getFilterIntegerAttributes(array $default_value)
 * @method  array      getFilterAttributeOverrides(array $default_value)
 * @method  array      getFilterRequiredAttributes(array $default_value)
 * @method  array      getFilterMediaBlacklist(array $default_value)
 * @method  array      getFilterMediaAttributes(array $default_value)
 * @method  array      getFilterSchemeWhitelist(array $default_value)
 * @method  array      getFilterWhitelistedTags(array $default_value)
 * @method  array      getFilterBlacklistedTags(array $default_value)
 * @method  string     getFilterImageProxyUrl()
 * @method  \Closure   getFilterImageProxyCallback()
 * @method  string     getFilterImageProxyProtocol()
 */
class Config
{
    /**
     * Contains all parameters.
     *
     * @var array
     */
    private $container = array();

    /**
     * Magic method to have any kind of setters or getters.
     *
     * @param string $name      Getter/Setter name
     * @param array  $arguments Method arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        $name = strtolower($name);
        $prefix = substr($name, 0, 3);
        $parameter = substr($name, 3);

        if ($prefix === 'set' && isset($arguments[0])) {
            $this->container[$parameter] = $arguments[0];

            return $this;
        } elseif ($prefix === 'get') {
            $default_value = isset($arguments[0]) ? $arguments[0] : null;

            return isset($this->container[$parameter]) ? $this->container[$parameter] : $default_value;
        }
    }
}
