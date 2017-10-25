<?php

namespace PicoFeed\Client;

/**
 * URL class.
 *
 * @author  Frederic Guillot
 */
class Url
{
    /**
     * URL.
     *
     * @var string
     */
    private $url = '';

    /**
     * URL components.
     *
     * @var array
     */
    private $components = array();

    /**
     * Constructor.
     *
     * @param string $url URL
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->components = parse_url($url) ?: array();

        // Issue with PHP < 5.4.7 and protocol relative url
        if (version_compare(PHP_VERSION, '5.4.7', '<') && $this->isProtocolRelative()) {
            $pos = strpos($this->components['path'], '/', 2);

            if ($pos === false) {
                $pos = strlen($this->components['path']);
            }

            $this->components['host'] = substr($this->components['path'], 2, $pos - 2);
            $this->components['path'] = substr($this->components['path'], $pos);
        }
    }

    /**
     * Shortcut method to get an absolute url from relative url.
     *
     * @static
     *
     * @param mixed $item_url    Unknown url (can be relative or not)
     * @param mixed $website_url Website url
     *
     * @return string
     */
    public static function resolve($item_url, $website_url)
    {
        $link = is_string($item_url) ? new self($item_url) : $item_url;
        $website = is_string($website_url) ? new self($website_url) : $website_url;

        if ($link->isRelativeUrl()) {
            if ($link->isRelativePath()) {
                return $link->getAbsoluteUrl($website->getBaseUrl($website->getBasePath()));
            }

            return $link->getAbsoluteUrl($website->getBaseUrl());
        } elseif ($link->isProtocolRelative()) {
            $link->setScheme($website->getScheme());
        }

        return $link->getAbsoluteUrl();
    }

    /**
     * Shortcut method to get a base url.
     *
     * @static
     *
     * @param string $url
     *
     * @return string
     */
    public static function base($url)
    {
        $link = new self($url);

        return $link->getBaseUrl();
    }

    /**
     * Get the base URL.
     *
     * @param string $suffix Add a suffix to the url
     *
     * @return string
     */
    public function getBaseUrl($suffix = '')
    {
        return $this->hasHost() ? $this->getScheme('://').$this->getHost().$this->getPort(':').$suffix : '';
    }

    /**
     * Get the absolute URL.
     *
     * @param string $base_url Use this url as base url
     *
     * @return string
     */
    public function getAbsoluteUrl($base_url = '')
    {
        if ($base_url) {
            $base = new self($base_url);
            $url = $base->getAbsoluteUrl().substr($this->getFullPath(), 1);
        } else {
            $url = $this->hasHost() ? $this->getBaseUrl().$this->getFullPath() : '';
        }

        return $url;
    }

    /**
     * Return true if the url is relative.
     *
     * @return bool
     */
    public function isRelativeUrl()
    {
        return !$this->hasScheme() && !$this->isProtocolRelative();
    }

    /**
     * Return true if the path is relative.
     *
     * @return bool
     */
    public function isRelativePath()
    {
        $path = $this->getPath();

        return empty($path) || $path{0}
        !== '/';
    }

    /**
     * Filters the path of a URI.
     *
     * Imported from Guzzle library: https://github.com/guzzle/psr7/blob/master/src/Uri.php#L568-L582
     *
     * @param $path
     *
     * @return string
     */
    public function filterPath($path, $charUnreserved = 'a-zA-Z0-9_\-\.~', $charSubDelims = '!\$&\'\(\)\*\+,;=')
    {
        return preg_replace_callback(
            '/(?:[^'.$charUnreserved.$charSubDelims.':@\/%]+|%(?![A-Fa-f0-9]{2}))/',
            function (array $matches) { return rawurlencode($matches[0]); },
            $path
        );
    }

    /**
     * Get the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->filterPath(empty($this->components['path']) ? '' : $this->components['path']);
    }

    /**
     * Get the base path.
     *
     * @return string
     */
    public function getBasePath()
    {
        $current_path = $this->getPath();

        $path = $this->isRelativePath() ? '/' : '';
        $path .= substr($current_path, -1) === '/' ? $current_path : dirname($current_path);

        return preg_replace('/\\\\\/|\/\//', '/', $path.'/');
    }

    /**
     * Get the full path (path + querystring + fragment).
     *
     * @return string
     */
    public function getFullPath()
    {
        $path = $this->isRelativePath() ? '/' : '';
        $path .= $this->getPath();
        $path .= empty($this->components['query']) ? '' : '?'.$this->components['query'];
        $path .= empty($this->components['fragment']) ? '' : '#'.$this->components['fragment'];

        return $path;
    }

    /**
     * Get the hostname.
     *
     * @return string
     */
    public function getHost()
    {
        return empty($this->components['host']) ? '' : $this->components['host'];
    }

    /**
     * Return true if the url has a hostname.
     *
     * @return bool
     */
    public function hasHost()
    {
        return !empty($this->components['host']);
    }

    /**
     * Get the scheme.
     *
     * @param string $suffix Suffix to add when there is a scheme
     *
     * @return string
     */
    public function getScheme($suffix = '')
    {
        return ($this->hasScheme() ? $this->components['scheme'] : 'http').$suffix;
    }

    /**
     * Set the scheme.
     *
     * @param string $scheme Set a scheme
     *
     * @return string
     */
    public function setScheme($scheme)
    {
        $this->components['scheme'] = $scheme;
    }

    /**
     * Return true if the url has a scheme.
     *
     * @return bool
     */
    public function hasScheme()
    {
        return !empty($this->components['scheme']);
    }

    /**
     * Get the port.
     *
     * @param string $prefix Prefix to add when there is a port
     *
     * @return string
     */
    public function getPort($prefix = '')
    {
        return $this->hasPort() ? $prefix.$this->components['port'] : '';
    }

    /**
     * Return true if the url has a port.
     *
     * @return bool
     */
    public function hasPort()
    {
        return !empty($this->components['port']);
    }

    /**
     * Return true if the url is protocol relative (start with //).
     *
     * @return bool
     */
    public function isProtocolRelative()
    {
        return strpos($this->url, '//') === 0;
    }
}
