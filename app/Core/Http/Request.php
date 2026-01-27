<?php

namespace Kanboard\Core\Http;

use Pimple\Container;
use Kanboard\Core\Base;

/**
 * Request class
 *
 * @package  http
 * @author   Frederic Guillot
 */
class Request extends Base
{
    /**
     * Pointer to PHP environment variables
     *
     * @access private
     * @var array
     */
    private $server;
    private $get;
    private $post;
    private $files;
    private $cookies;

    /**
     * Constructor
     *
     * @access public
     * @param \Pimple\Container $container
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $files
     * @param array $cookies
     */
    public function __construct(Container $container, array $server = array(), array $get = array(), array $post = array(), array $files = array(), array $cookies = array())
    {
        parent::__construct($container);
        $this->server = empty($server) ? $_SERVER : $server;
        $this->get = empty($get) ? $_GET : $get;
        $this->post = empty($post) ? $_POST : $post;
        $this->files = empty($files) ? $_FILES : $files;
        $this->cookies = empty($cookies) ? $_COOKIE : $cookies;
    }

    /**
     * Set GET parameters
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->get = array_merge($this->get, $params);
    }

    /**
     * Get query string string parameter
     *
     * @access public
     * @param  string   $name            Parameter name
     * @param  string   $default_value   Default value
     * @return string
     */
    public function getStringParam($name, $default_value = '')
    {
        return isset($this->get[$name]) ? $this->get[$name] : $default_value;
    }

    /**
     * Get query string integer parameter
     *
     * @access public
     * @param  string   $name            Parameter name
     * @param  integer  $default_value   Default value
     * @return integer
     */
    public function getIntegerParam($name, $default_value = 0)
    {
        return isset($this->get[$name]) && ctype_digit((string) $this->get[$name]) ? (int) $this->get[$name] : $default_value;
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
        if (! empty($this->post) && ! empty($this->post['csrf_token']) && $this->token->validateCSRFToken($this->post['csrf_token'])) {
            unset($this->post['csrf_token']);
            return $this->filterValues($this->post);
        }

        return array();
    }

    /**
     * Get POST values without modification
     *
     * @return array
     */
    public function getRawFormValues()
    {
        return $this->post;
    }

    /**
     * Get POST value without modification
     *
     * @param  $name
     * @return mixed|null
     */
    public function getRawValue($name)
    {
        return isset($this->post[$name]) ? $this->post[$name] : null;
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
     * Get the Json request body
     *
     * @access public
     * @param  bool  $enforceContentType
     * @return array
     */
    public function getJson($enforceContentType = true)
    {
        if ($enforceContentType && ! $this->isJsonContentType()) {
            return array();
        }

        return json_decode($this->getBody(), true) ?: array();
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
        if (isset($this->files[$name]['tmp_name'])) {
            return file_get_contents($this->files[$name]['tmp_name']);
        }

        return '';
    }

    /**
     * Get the path of an uploaded file
     *
     * @access public
     * @param  string   $name   Form file name
     * @return string
     */
    public function getFilePath($name)
    {
        return isset($this->files[$name]['tmp_name']) ? $this->files[$name]['tmp_name'] : '';
    }

    /**
     * Get info of an uploaded file
     *
     * @access public
     * @param  string   $name   Form file name
     * @return array
     */
    public function getFileInfo($name)
    {
        return isset($this->files[$name]) ? $this->files[$name] : array();
    }

    /**
     * Return HTTP method
     *
     * @access public
     * @return bool
     */
    public function getMethod()
    {
        return $this->getServerVariable('REQUEST_METHOD');
    }

    /**
     * Return true if the HTTP request is sent with the POST method
     *
     * @access public
     * @return bool
     */
    public function isPost()
    {
        return $this->getServerVariable('REQUEST_METHOD') === 'POST';
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
     * Check if the request Content-Type is JSON
     *
     * @access public
     * @return bool
     */
    public function isJsonContentType()
    {
        $contentType = $this->getServerVariable('CONTENT_TYPE');

        if ($contentType === '') {
            $contentType = $this->getServerVariable('HTTP_CONTENT_TYPE');
        }

        return stripos($contentType, 'application/json') === 0;
    }

    /**
     * Check if the page is requested through HTTPS
     *
     * Note: IIS return the value 'off' and other web servers an empty value when it's not HTTPS
     *
     * @access public
     * @return boolean
     */
    public function isHTTPS()
    {
        if ($this->getServerVariable('HTTP_X_FORWARDED_PROTO') === 'https') {
            return true;
        }

        return $this->getServerVariable('HTTPS') !== '' && $this->server['HTTPS'] !== 'off';
    }

    /**
     * Get cookie value
     *
     * @access public
     * @param  string $name
     * @return string
     */
    public function getCookie($name)
    {
        return isset($this->cookies[$name]) ? $this->cookies[$name] : '';
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
        return $this->getServerVariable($name);
    }

    /**
     * Get remote user
     *
     * @access public
     * @param  array $trustedProxyNetworks
     * @return string
     */
    public function getRemoteUser(array $trustedProxyNetworks = [])
    {
        if (! $this->isTrustedProxy($trustedProxyNetworks)) {
            return '';
        }
        return $this->getServerVariable(REVERSE_PROXY_USER_HEADER);
    }

    /**
     * Get remote email
     *
     * @access public
     * @param  array $trustedProxyNetworks
     * @return string
     */
    public function getRemoteEmail(array $trustedProxyNetworks = [])
    {
        if (! $this->isTrustedProxy($trustedProxyNetworks)) {
            return '';
        }
        return $this->getServerVariable(REVERSE_PROXY_EMAIL_HEADER);
    }

    /**
     * Get remote user full name
     *
     * @access public
     * @param  array $trustedProxyNetworks
     * @return string
     */
    public function getRemoteName(array $trustedProxyNetworks = [])
    {
        if (! $this->isTrustedProxy($trustedProxyNetworks)) {
            return '';
        }
        return $this->getServerVariable(REVERSE_PROXY_FULLNAME_HEADER);
    }

    /**
     * Returns query string
     *
     * @access public
     * @return string
     */
    public function getQueryString()
    {
        return $this->getServerVariable('QUERY_STRING');
    }

    /**
     * Return URI
     *
     * @access public
     * @return string
     */
    public function getUri()
    {
        return $this->getServerVariable('REQUEST_URI');
    }

    /**
     * Check if a redirect URI is safe (relative path)
     *
     * @access public
     * @param  string   $uri   Redirect URI
     * @return bool
     */
    public function isSafeRedirectUri($uri)
    {
        $uri = trim($uri);
        if ($uri === '') {
            return false;
        }

        // Reject backslashes
        if (str_contains($uri, '\\')) {
            return false;
        }

        // Reject if it starts with // (protocol-relative)
        if (str_starts_with($uri, '//')) {
            return false;
        }

        // Reject if it does not start with a slash (relative path)
        if (! str_starts_with($uri, '/')) {
            return false;
        }

        $parsedUrl = parse_url($uri);
        if ($parsedUrl === false) {
            return false;
        }

        // Reject if it contains a scheme or host (partial or full URL)
        if (isset($parsedUrl['scheme']) || isset($parsedUrl['host'])) {
            return false;
        }

        return true;
    }

    /**
     * Get the user agent
     *
     * @access public
     * @return string
     */
    public function getUserAgent()
    {
        return empty($this->server['HTTP_USER_AGENT']) ? t('Unknown') : $this->server['HTTP_USER_AGENT'];
    }

    /**
     * Get the client IP address
     *
     * It returns the proxy IP address if the request is sent through a reverse proxy or the direct client IP address otherwise.
     *
     * @access public
     * @return string
     */
    public function getClientIpAddress()
    {
        return $this->getServerVariable('REMOTE_ADDR');
    }

    /**
     * Get the real user IP address considering trusted proxy headers and networks
     *
     * @access public
     * @param  array   $trustedProxyHeaders  List of trusted proxy headers (default: TRUSTED_PROXY_HEADERS constant)
     * @param  array   $trustedProxyNetworks List of trusted proxy networks (default: TRUSTED_PROXY_NETWORKS constant)
     * @return string
     */
    public function getIpAddress(array $trustedProxyHeaders = [], array $trustedProxyNetworks = [])
    {
        $trustedProxyHeaders = array_filter(array_map('trim', $trustedProxyHeaders ?: explode(',', TRUSTED_PROXY_HEADERS)));
        $useProxyHeaders = ! empty($trustedProxyHeaders) && $this->isTrustedProxy($trustedProxyNetworks);
        $keys = $useProxyHeaders ? $trustedProxyHeaders : [];

        foreach ($keys as $key) {
            if ($this->getServerVariable($key) !== '') {
                foreach (explode(',', $this->server[$key]) as $ipAddress) {
                    $ipAddress = trim($ipAddress);
                    if (filter_var($ipAddress, FILTER_VALIDATE_IP)) {
                        return $ipAddress;
                    }
                }
            }
        }

        return $this->getClientIpAddress();
    }

    /**
     * Get start time
     *
     * @access public
     * @return float
     */
    public function getStartTime()
    {
        return $this->getServerVariable('REQUEST_TIME_FLOAT') ?: 0;
    }

    /**
     * Get server variable
     *
     * @access public
     * @param  string $variable
     * @return string
     */
    public function getServerVariable($variable)
    {
        return isset($this->server[$variable]) ? $this->server[$variable] : '';
    }

    protected function filterValues(array $values)
    {
        foreach ($values as $key => $value) {

            // IE11 Workaround when submitting multipart/form-data
            if (strpos($key, '-----------------------------') === 0) {
                unset($values[$key]);
            }
        }

        return $values;
    }

    /**
     * Check if an IP address belongs to a trusted proxy network
     *
     * @access public
     * @param  array       $trustedProxyNetworks
     * @return bool
     */
    public function isTrustedProxy(array $trustedProxyNetworks = [])
    {
        $ipAddress = $this->getClientIpAddress();
        if ($ipAddress === '') {
            return false;
        }

        $trustedProxyNetworks = array_filter(array_map('trim', $trustedProxyNetworks ?: explode(',', TRUSTED_PROXY_NETWORKS)));
        if (empty($trustedProxyNetworks)) {
            return false;
        }

        $this->logger->debug('Checking if IP address {ip} belongs to trusted proxy networks: {networks}', ['ip' => $ipAddress, 'networks' => implode(', ', $trustedProxyNetworks)]);

        return $this->isIpInNetworks($ipAddress, $trustedProxyNetworks);
    }

    /**
     * Check if an IP belongs to any of the provided networks
     *
     * @access protected
     * @param string $ipAddress
     * @param array $networks
     * @return bool
     */
    protected function isIpInNetworks($ipAddress, array $networks)
    {
        if (! filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            return false;
        }

        $ipBinary = inet_pton($ipAddress);

        foreach ($networks as $network) {
            if ($network === '') {
                continue;
            }

            $mask = null;
            if (strpos($network, '/') !== false) {
                list($networkAddress, $mask) = explode('/', $network, 2);
            } else {
                $networkAddress = $network;
            }

            if (! filter_var($networkAddress, FILTER_VALIDATE_IP)) {
                continue;
            }

            $networkBinary = inet_pton($networkAddress);

            if ($networkBinary === false || strlen($networkBinary) !== strlen($ipBinary)) {
                continue;
            }

            $maxMask = strlen($networkBinary) * 8;
            $mask = ($mask === null || $mask === '') ? $maxMask : max(0, min((int) $mask, $maxMask));

            if ($this->ipMatchesNetwork($ipBinary, $networkBinary, $mask)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Perform a binary comparison between an IP and a network mask
     *
     * @access protected
     * @param string $ipBinary
     * @param string $networkBinary
     * @param int    $mask
     * @return bool
     */
    protected function ipMatchesNetwork($ipBinary, $networkBinary, $mask)
    {
        if ($mask === 0) {
            return true;
        }

        $bytes = (int) floor($mask / 8);
        $bits = $mask % 8;

        if ($bytes > 0 && strncmp($ipBinary, $networkBinary, $bytes) !== 0) {
            return false;
        }

        if ($bits === 0) {
            return true;
        }

        $maskByte = ~((1 << (8 - $bits)) - 1) & 0xFF;
        $ipByte = ord($ipBinary[$bytes]);
        $networkByte = ord($networkBinary[$bytes]);

        return ($ipByte & $maskByte) === ($networkByte & $maskByte);
    }
}
