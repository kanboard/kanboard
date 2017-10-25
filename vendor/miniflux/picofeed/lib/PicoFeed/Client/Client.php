<?php

namespace PicoFeed\Client;

use DateTime;
use Exception;
use LogicException;
use PicoFeed\Logging\Logger;
use PicoFeed\Config\Config;

/**
 * Client class.
 *
 * @author  Frederic Guillot
 */
abstract class Client
{
    /**
     * Flag that say if the resource have been modified.
     *
     * @var bool
     */
    private $is_modified = true;

    /**
     * HTTP Content-Type.
     *
     * @var string
     */
    private $content_type = '';

    /**
     * HTTP encoding.
     *
     * @var string
     */
    private $encoding = '';

    /**
     * HTTP request headers.
     *
     * @var array
     */
    protected $request_headers = array();

    /**
     * HTTP Etag header.
     *
     * @var string
     */
    protected $etag = '';

    /**
     * HTTP Last-Modified header.
     *
     * @var string
     */
    protected $last_modified = '';

    /**
     * Expiration DateTime
     *
     * @var DateTime
     */
    protected $expiration = null;

    /**
     * Proxy hostname.
     *
     * @var string
     */
    protected $proxy_hostname = '';

    /**
     * Proxy port.
     *
     * @var int
     */
    protected $proxy_port = 3128;

    /**
     * Proxy username.
     *
     * @var string
     */
    protected $proxy_username = '';

    /**
     * Proxy password.
     *
     * @var string
     */
    protected $proxy_password = '';

    /**
     * Basic auth username.
     *
     * @var string
     */
    protected $username = '';

    /**
     * Basic auth password.
     *
     * @var string
     */
    protected $password = '';

    /**
     * Client connection timeout.
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * User-agent.
     *
     * @var string
     */
    protected $user_agent = 'PicoFeed (https://github.com/miniflux/picoFeed)';

    /**
     * Real URL used (can be changed after a HTTP redirect).
     *
     * @var string
     */
    protected $url = '';

    /**
     * Page/Feed content.
     *
     * @var string
     */
    protected $content = '';

    /**
     * Number maximum of HTTP redirections to avoid infinite loops.
     *
     * @var int
     */
    protected $max_redirects = 5;

    /**
     * Maximum size of the HTTP body response.
     *
     * @var int
     */
    protected $max_body_size = 2097152; // 2MB

    /**
     * HTTP response status code.
     *
     * @var int
     */
    protected $status_code = 0;

    /**
     * Enables direct passthrough to requesting client.
     *
     * @var bool
     */
    protected $passthrough = false;

    /**
     * Do the HTTP request.
     *
     * @abstract
     *
     * @return array
     */
    abstract public function doRequest();

    /**
     * Get client instance: curl or stream driver.
     *
     * @static
     *
     * @return \PicoFeed\Client\Client
     */
    public static function getInstance()
    {
        if (function_exists('curl_init')) {
            return new Curl();
        } elseif (ini_get('allow_url_fopen')) {
            return new Stream();
        }

        throw new LogicException('You must have "allow_url_fopen=1" or curl extension installed');
    }

    /**
     * Add HTTP Header to the request.
     *
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->request_headers = $headers;
    }

    /**
     * Perform the HTTP request.
     *
     * @param string $url URL
     *
     * @return Client
     */
    public function execute($url = '')
    {
        if ($url !== '') {
            $this->url = $url;
        }

        Logger::setMessage(get_called_class().' Fetch URL: '.$this->url);
        Logger::setMessage(get_called_class().' Etag provided: '.$this->etag);
        Logger::setMessage(get_called_class().' Last-Modified provided: '.$this->last_modified);

        $response = $this->doRequest();

        $this->status_code = $response['status'];
        $this->handleNotModifiedResponse($response);
        $this->handleErrorResponse($response);
        $this->handleNormalResponse($response);

        $this->expiration = $this->parseExpiration($response['headers']);
        Logger::setMessage(get_called_class().' Expiration: '.$this->expiration->format(DATE_ISO8601));

        return $this;
    }

    /**
     * Handle not modified response.
     *
     * @param array $response Client response
     */
    protected function handleNotModifiedResponse(array $response)
    {
        if ($response['status'] == 304) {
            $this->is_modified = false;
        } elseif ($response['status'] == 200) {
            $this->is_modified = $this->hasBeenModified($response, $this->etag, $this->last_modified);
            $this->etag = $this->getHeader($response, 'ETag');
            $this->last_modified = $this->getHeader($response, 'Last-Modified');
        }

        if ($this->is_modified === false) {
            Logger::setMessage(get_called_class().' Resource not modified');
        }
    }

    /**
     * Handle Http Error codes
     *
     * @param array $response Client response
     * @throws ForbiddenException
     * @throws InvalidUrlException
     * @throws UnauthorizedException
     */
    protected function handleErrorResponse(array $response)
    {
        $status = $response['status'];
        if ($status == 401) {
            throw new UnauthorizedException('Wrong or missing credentials');
        } else if ($status == 403) {
            throw new ForbiddenException('Not allowed to access resource');
        } else if ($status == 404) {
            throw new InvalidUrlException('Resource not found');
        }
    }

    /**
     * Handle normal response.
     *
     * @param array $response Client response
     */
    protected function handleNormalResponse(array $response)
    {
        if ($response['status'] == 200) {
            $this->content = $response['body'];
            $this->content_type = $this->findContentType($response);
            $this->encoding = $this->findCharset();
        }
    }

    /**
     * Check if a request has been modified according to the parameters.
     *
     * @param array  $response
     * @param string $etag
     * @param string $lastModified
     *
     * @return bool
     */
    private function hasBeenModified($response, $etag, $lastModified)
    {
        $headers = array(
            'Etag' => $etag,
            'Last-Modified' => $lastModified,
        );

        // Compare the values for each header that is present
        $presentCacheHeaderCount = 0;
        foreach ($headers as $key => $value) {
            if (isset($response['headers'][$key])) {
                if ($response['headers'][$key] !== $value) {
                    return true;
                }
                ++$presentCacheHeaderCount;
            }
        }

        // If at least one header is present and the values match, the response
        // was not modified
        if ($presentCacheHeaderCount > 0) {
            return false;
        }

        return true;
    }

    /**
     * Find content type from response headers.
     *
     * @param array $response Client response
     *
     * @return string
     */
    public function findContentType(array $response)
    {
        return strtolower($this->getHeader($response, 'Content-Type'));
    }

    /**
     * Find charset from response headers.
     *
     * @return string
     */
    public function findCharset()
    {
        $result = explode('charset=', $this->content_type);

        return isset($result[1]) ? $result[1] : '';
    }

    /**
     * Get header value from a client response.
     *
     * @param array  $response Client response
     * @param string $header   Header name
     *
     * @return string
     */
    public function getHeader(array $response, $header)
    {
        return isset($response['headers'][$header]) ? $response['headers'][$header] : '';
    }

    /**
     * Set the Last-Modified HTTP header.
     *
     * @param string $last_modified Header value
     *
     * @return \PicoFeed\Client\Client
     */
    public function setLastModified($last_modified)
    {
        $this->last_modified = $last_modified;

        return $this;
    }

    /**
     * Get the value of the Last-Modified HTTP header.
     *
     * @return string
     */
    public function getLastModified()
    {
        return $this->last_modified;
    }

    /**
     * Set the value of the Etag HTTP header.
     *
     * @param string $etag Etag HTTP header value
     *
     * @return \PicoFeed\Client\Client
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    /**
     * Get the Etag HTTP header value.
     *
     * @return string
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * Get the final url value.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the url.
     *
     * @param  $url
     * @return string
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get the HTTP response status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * Get the body of the HTTP response.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get the content type value from HTTP headers.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * Get the encoding value from HTTP headers.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Return true if the remote resource has changed.
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->is_modified;
    }

    /**
     * return true if passthrough mode is enabled.
     *
     * @return bool
     */
    public function isPassthroughEnabled()
    {
        return $this->passthrough;
    }

    /**
     * Set connection timeout.
     *
     * @param int $timeout Connection timeout
     *
     * @return \PicoFeed\Client\Client
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout ?: $this->timeout;

        return $this;
    }

    /**
     * Set a custom user agent.
     *
     * @param string $user_agent User Agent
     *
     * @return \PicoFeed\Client\Client
     */
    public function setUserAgent($user_agent)
    {
        $this->user_agent = $user_agent ?: $this->user_agent;

        return $this;
    }

    /**
     * Set the maximum number of HTTP redirections.
     *
     * @param int $max Maximum
     *
     * @return \PicoFeed\Client\Client
     */
    public function setMaxRedirections($max)
    {
        $this->max_redirects = $max ?: $this->max_redirects;

        return $this;
    }

    /**
     * Set the maximum size of the HTTP body.
     *
     * @param int $max Maximum
     *
     * @return \PicoFeed\Client\Client
     */
    public function setMaxBodySize($max)
    {
        $this->max_body_size = $max ?: $this->max_body_size;

        return $this;
    }

    /**
     * Set the proxy hostname.
     *
     * @param string $hostname Proxy hostname
     *
     * @return \PicoFeed\Client\Client
     */
    public function setProxyHostname($hostname)
    {
        $this->proxy_hostname = $hostname ?: $this->proxy_hostname;

        return $this;
    }

    /**
     * Set the proxy port.
     *
     * @param int $port Proxy port
     *
     * @return \PicoFeed\Client\Client
     */
    public function setProxyPort($port)
    {
        $this->proxy_port = $port ?: $this->proxy_port;

        return $this;
    }

    /**
     * Set the proxy username.
     *
     * @param string $username Proxy username
     *
     * @return \PicoFeed\Client\Client
     */
    public function setProxyUsername($username)
    {
        $this->proxy_username = $username ?: $this->proxy_username;

        return $this;
    }

    /**
     * Set the proxy password.
     *
     * @param string $password Password
     *
     * @return \PicoFeed\Client\Client
     */
    public function setProxyPassword($password)
    {
        $this->proxy_password = $password ?: $this->proxy_password;

        return $this;
    }

    /**
     * Set the username.
     *
     * @param string $username Basic Auth username
     *
     * @return \PicoFeed\Client\Client
     */
    public function setUsername($username)
    {
        $this->username = $username ?: $this->username;

        return $this;
    }

    /**
     * Set the password.
     *
     * @param string $password Basic Auth Password
     *
     * @return \PicoFeed\Client\Client
     */
    public function setPassword($password)
    {
        $this->password = $password ?: $this->password;

        return $this;
    }

    /**
     * Enable the passthrough mode.
     *
     * @return \PicoFeed\Client\Client
     */
    public function enablePassthroughMode()
    {
        $this->passthrough = true;

        return $this;
    }

    /**
     * Disable the passthrough mode.
     *
     * @return \PicoFeed\Client\Client
     */
    public function disablePassthroughMode()
    {
        $this->passthrough = false;

        return $this;
    }

    /**
     * Set config object.
     *
     * @param \PicoFeed\Config\Config $config Config instance
     *
     * @return \PicoFeed\Client\Client
     */
    public function setConfig(Config $config)
    {
        if ($config !== null) {
            $this->setTimeout($config->getClientTimeout());
            $this->setUserAgent($config->getClientUserAgent());
            $this->setMaxRedirections($config->getMaxRedirections());
            $this->setMaxBodySize($config->getMaxBodySize());
            $this->setProxyHostname($config->getProxyHostname());
            $this->setProxyPort($config->getProxyPort());
            $this->setProxyUsername($config->getProxyUsername());
            $this->setProxyPassword($config->getProxyPassword());
        }

        return $this;
    }

    /**
     * Return true if the HTTP status code is a redirection
     *
     * @access protected
     * @param  integer  $code
     * @return boolean
     */
    public function isRedirection($code)
    {
        return $code == 301 || $code == 302 || $code == 303 || $code == 307;
    }

    public function parseExpiration(HttpHeaders $headers)
    {
        try {

            if (isset($headers['Cache-Control'])) {
                if (preg_match('/s-maxage=(\d+)/', $headers['Cache-Control'], $matches)) {
                    return new DateTime('+' . $matches[1] . ' seconds');
                } else if (preg_match('/max-age=(\d+)/', $headers['Cache-Control'], $matches)) {
                    return new DateTime('+' . $matches[1] . ' seconds');
                }
            }

            if (! empty($headers['Expires'])) {
                return new DateTime($headers['Expires']);
            }
        } catch (Exception $e) {
            Logger::setMessage('Unable to parse expiration date: '.$e->getMessage());
        }

        return new DateTime();
    }

    /**
     * Get expiration date time from "Expires" or "Cache-Control" headers
     *
     * @return DateTime
     */
    public function getExpiration()
    {
        return $this->expiration ?: new DateTime();
    }
}
