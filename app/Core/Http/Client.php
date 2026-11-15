<?php

namespace Kanboard\Core\Http;

use Kanboard\Core\Base;
use Kanboard\Job\HttpAsyncJob;

/**
 * HTTP client
 *
 * @package  Kanboard\Core\Http
 * @author   Frederic Guillot
 */
class Client extends Base
{
    /**
     * Address validated for the URL passed to checkUrl()
     *
     * @access private
     * @var array|null
     */
    private $pinnedAddresses = null;

    /**
     * HTTP client user agent
     *
     * @var string
     */
    const HTTP_USER_AGENT = 'Kanboard';

    /**
     * Send a GET HTTP request
     *
     * @access public
     * @param  string     $url
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @param  bool       $followRedirects
     * @return string
     */
    public function get($url, array $headers = [], $raiseForErrors = false, $followRedirects = true)
    {
        return $this->doRequest('GET', $url, '', $headers, $raiseForErrors, $followRedirects);
    }

    /**
     * Send a GET HTTP request and parse JSON response
     *
     * @access public
     * @param  string     $url
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @param  bool       $followRedirects
     * @return array
     */
    public function getJson($url, array $headers = [], $raiseForErrors = false, $followRedirects = true)
    {
        $response = $this->doRequest('GET', $url, '', array_merge(['Accept: application/json'], $headers), $raiseForErrors, $followRedirects);
        return json_decode($response, true) ?: [];
    }

    /**
     * Send a POST HTTP request encoded in JSON
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @param  bool       $followRedirects
     * @return string
     */
    public function postJson($url, array $data, array $headers = [], $raiseForErrors = false, $followRedirects = true)
    {
        return $this->doRequest(
            'POST',
            $url,
            json_encode($data),
            array_merge(['Content-type: application/json'], $headers),
            $raiseForErrors,
            $followRedirects
        );
    }

    /**
     * Send a POST HTTP request encoded in JSON (Fire and forget)
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     */
    public function postJsonAsync($url, array $data, array $headers = [], $raiseForErrors = false)
    {
        $this->queueManager->push(HttpAsyncJob::getInstance($this->container)->withParams(
            'POST',
            $url,
            json_encode($data),
            array_merge(['Content-type: application/json'], $headers),
            $raiseForErrors
        ));
    }

    /**
     * Send a POST HTTP request encoded in www-form-urlencoded
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @return string
     */
    public function postForm($url, array $data, array $headers = [], $raiseForErrors = false)
    {
        return $this->doRequest(
            'POST',
            $url,
            http_build_query($data),
            array_merge(['Content-type: application/x-www-form-urlencoded'], $headers),
            $raiseForErrors
        );
    }

    /**
     * Send a POST HTTP request encoded in www-form-urlencoded (fire and forget)
     *
     * @access public
     * @param  string     $url
     * @param  array      $data
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     */
    public function postFormAsync($url, array $data, array $headers = [], $raiseForErrors = false)
    {
        $this->queueManager->push(HttpAsyncJob::getInstance($this->container)->withParams(
            'POST',
            $url,
            http_build_query($data),
            array_merge(['Content-type: application/x-www-form-urlencoded'], $headers),
            $raiseForErrors
        ));
    }

    /**
     * Make the HTTP request with cURL if detected, socket otherwise
     *
     * @access public
     * @param  string     $method
     * @param  string     $url
     * @param  string     $content
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @param  bool       $followRedirects
     * @return string
     */
    public function doRequest($method, $url, $content, array $headers, $raiseForErrors = false, $followRedirects = true)
    {
        $requestBody = '';

        if (! empty($url)) {
            if (function_exists('curl_version')) {
                if (DEBUG) {
                    $this->logger->debug('HttpClient::doRequest: cURL detected');
                }
                $requestBody = $this->doRequestWithCurl($method, $url, $content, $headers, $raiseForErrors, $followRedirects);
            } else {
                if (DEBUG) {
                    $this->logger->debug('HttpClient::doRequest: using socket');
                }
                $requestBody = $this->doRequestWithSocket($method, $url, $content, $headers, $raiseForErrors, $followRedirects);
            }
        }

        return $requestBody;
    }

    /**
     * Make the HTTP request with socket
     *
     * @access private
     * @param  string     $method
     * @param  string     $url
     * @param  string     $content
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @param  bool       $followRedirects
     * @return string
     */
    private function doRequestWithSocket($method, $url, $content, array $headers, $raiseForErrors = false, $followRedirects = true)
    {
        $startTime = microtime(true);
        $stream = @fopen(trim($url), 'r', false, stream_context_create($this->getContext($method, $content, $headers, $raiseForErrors, $followRedirects)));

        if (! is_resource($stream)) {
            $this->logger->error('HttpClient: request failed ('.$url.')');

            if ($raiseForErrors) {
                throw new ClientException('Unreachable URL: '.$url);
            }

            return '';
        }

        $body = stream_get_contents($stream);
        $metadata = stream_get_meta_data($stream);

        if ($raiseForErrors && array_key_exists('wrapper_data', $metadata)) {
            $statusCode = $this->getStatusCode($metadata['wrapper_data']);

            if ($statusCode >= 400) {
                throw new InvalidStatusException('Request failed with status code '.$statusCode, $statusCode, $body);
            }
        }

        if (DEBUG) {
            $this->logger->debug('HttpClient: url='.$url);
            $this->logger->debug('HttpClient: headers='.var_export($headers, true));
            $this->logger->debug('HttpClient: payload='.$content);
            $this->logger->debug('HttpClient: metadata='.var_export($metadata, true));
            $this->logger->debug('HttpClient: body='.$body);
            $this->logger->debug('HttpClient: executionTime='.(microtime(true) - $startTime));
        }

        return $body;
    }


    /**
     * Make the HTTP request with cURL
     *
     * @access private
     * @param  string     $method
     * @param  string     $url
     * @param  string     $content
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @param  bool       $followRedirects
     * @return string
     */
    private function doRequestWithCurl($method, $url, $content, array $headers, $raiseForErrors = false, $followRedirects = true)
    {
        $startTime = microtime(true);
        $curlSession = @curl_init();

        curl_setopt($curlSession, CURLOPT_URL, trim($url));
        curl_setopt($curlSession, CURLOPT_USERAGENT, self::HTTP_USER_AGENT);
        curl_setopt($curlSession, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlSession, CURLOPT_TIMEOUT, HTTP_TIMEOUT);
        curl_setopt($curlSession, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curlSession, CURLOPT_MAXREDIRS, $followRedirects ? HTTP_MAX_REDIRECTS : 0);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, $followRedirects);

        if ('POST' === $method) {
            curl_setopt($curlSession, CURLOPT_POST, true);
            curl_setopt($curlSession, CURLOPT_POSTFIELDS, $content);
        } elseif ('PUT' === $method) {
            curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curlSession, CURLOPT_POST, true);
            curl_setopt($curlSession, CURLOPT_POSTFIELDS, $content);
        }

        if (! empty($headers)) {
            curl_setopt($curlSession, CURLOPT_HTTPHEADER, $headers);
        }

        if (HTTP_VERIFY_SSL_CERTIFICATE === false) {
            curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
        }

        if (! HTTP_PROXY_HOSTNAME) {
            foreach ($this->getResolveOptions($url) as $resolveOption) {
                curl_setopt($curlSession, CURLOPT_RESOLVE, $resolveOption);
            }
        }

        if (HTTP_PROXY_HOSTNAME) {
            curl_setopt($curlSession, CURLOPT_PROXY, HTTP_PROXY_HOSTNAME);
            curl_setopt($curlSession, CURLOPT_PROXYPORT, HTTP_PROXY_PORT);
            curl_setopt($curlSession, CURLOPT_NOPROXY, HTTP_PROXY_EXCLUDE);
        }

        if (HTTP_PROXY_USERNAME) {
            curl_setopt($curlSession, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($curlSession, CURLOPT_PROXYUSERPWD, HTTP_PROXY_USERNAME.':'.HTTP_PROXY_PASSWORD);
        }

        $body = curl_exec($curlSession);

        if ($body === false) {
            $errorMsg = curl_error($curlSession);

            $this->logger->error('HttpClient: request failed ('.$url.' - '.$errorMsg.')');

            if ($raiseForErrors) {
                throw new ClientException('Unreachable URL: '.$url.' ('.$errorMsg.')');
            }

            return '';
        }

        if ($raiseForErrors) {
            $statusCode = curl_getinfo($curlSession, CURLINFO_RESPONSE_CODE);

            if ($statusCode >= 400) {
                throw new InvalidStatusException('Request failed with status code '.$statusCode, $statusCode, $body);
            }
        }

        if (DEBUG) {
            $this->logger->debug('HttpClient: url='.$url);
            $this->logger->debug('HttpClient: headers='.var_export($headers, true));
            $this->logger->debug('HttpClient: payload='.$content);
            $this->logger->debug('HttpClient: metadata='.var_export(curl_getinfo($curlSession), true));
            $this->logger->debug('HttpClient: body='.$body);
            $this->logger->debug('HttpClient: executionTime='.(microtime(true) - $startTime));
        }

        return $body;
    }

    /**
     * Get stream context
     *
     * @access private
     * @param  string     $method
     * @param  string     $content
     * @param  string[]   $headers
     * @param  bool       $raiseForErrors
     * @param  bool       $followRedirects
     * @return array
     */
    private function getResolveOptions($url)
    {
        if (empty($this->pinnedAddresses)) {
            return array();
        }

        $components = parse_url($url);

        if (empty($components['host'])) {
            return array();
        }

        $host = trim($components['host'], '[]');
        $port = isset($components['port']) ? (int) $components['port'] : (isset($components['scheme']) && strtolower($components['scheme']) === 'https' ? 443 : 80);
        $entries = array();

        foreach ($this->pinnedAddresses as $address) {
            $formatted = strpos($address, ':') !== false ? '['.$address.']' : $address;
            $entries[] = $host.':'.$port.':'.$formatted;
        }

        return array($entries);
    }

    private function getContext($method, $content, array $headers, $raiseForErrors = false, $followRedirects = true)
    {
        $default_headers = [
            'User-Agent: '.self::HTTP_USER_AGENT,
            'Connection: close',
        ];

        if (HTTP_PROXY_USERNAME) {
            $default_headers[] = 'Proxy-Authorization: Basic '.base64_encode(HTTP_PROXY_USERNAME.':'.HTTP_PROXY_PASSWORD);
        }

        $headers = array_merge($default_headers, $headers);

        $context = [
            'http' => [
                'method' => $method,
                'protocol_version' => 1.1,
                'timeout' => HTTP_TIMEOUT,
                'max_redirects' => $followRedirects ? HTTP_MAX_REDIRECTS : 0,
                'follow_location' => $followRedirects ? 1 : 0,
                'header' => implode("\r\n", $headers),
                'content' => $content,
                'ignore_errors' => $raiseForErrors,
            ]
        ];

        if (HTTP_PROXY_HOSTNAME) {
            $context['http']['proxy'] = 'tcp://'.HTTP_PROXY_HOSTNAME.':'.HTTP_PROXY_PORT;
            $context['http']['request_fulluri'] = true;
        }

        if (HTTP_VERIFY_SSL_CERTIFICATE === false) {
            $context['ssl'] = [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ];
        }

        return $context;
    }

    private function getStatusCode(array $lines)
    {
        $status = 200;

        foreach ($lines as $line) {
            if (strpos($line, 'HTTP/1') === 0) {
                $status = (int) substr($line, 9, 3);
            }
        }

        return $status;
    }

    /**
     * Get backend used for making HTTP connections
     *
     * @access public
     * @return string
     */
    public static function backend()
    {
        return function_exists('curl_version') ? 'cURL' : 'socket';
    }

    /**
     * Check if an IP address is private
     *
     * @access public
     * @param  string     $ip
     * @return bool
     */
    public function isPrivateIpAddress($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }

        // Carrier-grade NAT (100.64.0.0/10) is not covered by the PHP flags
        return $this->isInCidr($ip, '100.64.0.0/10');
    }

    /**
     * Check if an IPv4 address belongs to a CIDR range
     *
     * @access private
     * @param  string  $ip
     * @param  string  $cidr
     * @return boolean
     */
    private function isInCidr($ip, $cidr)
    {
        list($subnet, $mask) = explode('/', $cidr);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $maskLong = -1 << (32 - (int) $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    /**
     * Parse an IPv4 address written in one of the numeric forms accepted by
     * inet_aton() and by curl (hexadecimal, octal, decimal, 1 to 4 parts)
     *
     * @access private
     * @param  string      $host
     * @return string|bool Normalized dotted address, or false when not numeric
     */
    private function parseNumericIpv4($host)
    {
        $parts = explode('.', $host);

        if (count($parts) > 4) {
            return false;
        }

        $values = array();

        foreach ($parts as $part) {
            if ($part === '') {
                return false;
            }

            if (preg_match('/^0[xX]([0-9a-fA-F]+)$/', $part, $match)) {
                $values[] = hexdec($match[1]);
            } elseif (preg_match('/^0[0-7]+$/', $part)) {
                $values[] = octdec($part);
            } elseif (preg_match('/^[0-9]+$/', $part)) {
                $values[] = (int) $part;
            } else {
                return false;
            }
        }

        $last = array_pop($values);

        // The last part fills all the remaining bytes
        if ($last >= (1 << (8 * (4 - count($values))))) {
            return false;
        }

        $ip = 0;

        foreach ($values as $index => $value) {
            if ($value > 255) {
                return false;
            }

            $ip |= $value << (8 * (3 - $index));
        }

        return long2ip($ip | $last);
    }

    /**
     * Get the addresses a host resolves to
     *
     * Returns null when the host is a valid IP literal, an empty array when it
     * cannot be resolved, and the resolved addresses otherwise.
     *
     * @access private
     * @param  string     $host
     * @return array|null
     */
    private function resolveHost($host)
    {
        $host = trim($host, '[]');

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return array($host);
        }

        $numeric = $this->parseNumericIpv4($host);

        if ($numeric !== false) {
            return array($numeric);
        }

        $addresses = gethostbynamel($host);

        if ($addresses === false) {
            $addresses = array();
        }

        if (function_exists('dns_get_record')) {
            $records = @dns_get_record($host, DNS_AAAA);

            if (is_array($records)) {
                foreach ($records as $record) {
                    if (isset($record['type']) && $record['type'] === 'AAAA' && isset($record['ipv6'])) {
                        $addresses[] = $record['ipv6'];
                    }
                }
            }
        }

        return array_values(array_unique($addresses));
    }

    /**
     * Resolve a URL and remember its address so the request reuses it
     *
     * Pins the validated address for the next request to this URL, which
     * prevents a short TTL record from pointing to another address when the
     * HTTP client resolves the host a second time.
     *
     * @access public
     * @param  string      $url
     * @return string|bool false when the URL must not be fetched
     */
    public function checkUrl($url)
    {
        $this->pinnedAddresses = null;

        if ($this->isPrivateURL($url)) {
            return false;
        }

        $this->pinnedAddresses = $this->resolveHost(trim((string) parse_url($url, PHP_URL_HOST), '[]'));

        return $url;
    }

    /**
     * Check if a URL is private (RFC1918, localhost, etc.)
     *
     * @access public
     * @param  string     $url
     * @return bool
     */
    public function isPrivateURL($url)
    {
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['scheme']) || !in_array(strtolower($parsedUrl['scheme']), ['http', 'https'], true)) {
            return false;
        }

        if (!isset($parsedUrl['host'])) {
            return false;
        }

        $host = trim($parsedUrl['host']);

        if ($host === '') {
            return false;
        }

        $addresses = $this->resolveHost($host);

        // A host that cannot be resolved locally is not evidence of a private
        // target: proxy only setups have no DNS. It is rejected later by the
        // HTTP client itself when the connection cannot be established.
        if (empty($addresses)) {
            return false;
        }

        foreach ($addresses as $address) {
            if ($this->isPrivateIpAddress($address)) {
                return true;
            }
        }

        return false;
    }
}
