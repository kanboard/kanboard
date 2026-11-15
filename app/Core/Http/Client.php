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
     * HTTP client user agent
     *
     * @var string
     */
    const HTTP_USER_AGENT = 'Kanboard';

    /**
     * IPv4 networks that are not publicly routable
     *
     * @var string[]
     */
    private const NON_PUBLIC_IPV4_NETWORKS = [
        '0.0.0.0/8',       // "This" network
        '10.0.0.0/8',      // Private use
        '100.64.0.0/10',   // Shared address space (carrier-grade NAT)
        '127.0.0.0/8',     // Loopback
        '169.254.0.0/16',  // Link-local (cloud metadata services)
        '172.16.0.0/12',   // Private use
        '192.0.0.0/24',    // IETF protocol assignments
        '192.0.2.0/24',    // Documentation
        '192.88.99.0/24',  // 6to4 relay anycast
        '192.168.0.0/16',  // Private use
        '198.18.0.0/15',   // Benchmarking
        '198.51.100.0/24', // Documentation
        '203.0.113.0/24',  // Documentation
        '224.0.0.0/4',     // Multicast
        '240.0.0.0/4',     // Reserved and limited broadcast
    ];

    /**
     * IPv6 networks that are not publicly routable within the global unicast range 2000::/3
     *
     * @var string[]
     */
    private const NON_PUBLIC_IPV6_NETWORKS = [
        '2001::/23',     // IETF protocol assignments (Teredo, benchmarking, ORCHID)
        '2001:db8::/32', // Documentation
        '3fff::/20',     // Documentation
    ];

    /**
     * Refuse connections to non-public IP addresses
     *
     * @var bool
     */
    private $blockPrivateNetworks = false;

    /**
     * Get a client that only connects to publicly routable IP addresses
     *
     * The host is resolved once, every address is validated and the connection
     * is pinned to the validated address, so the address checked is the address
     * connected to. Redirects are never followed by this client.
     *
     * @access public
     * @return static
     */
    public function withPrivateNetworksBlocked()
    {
        $client = clone $this;
        $client->blockPrivateNetworks = true;
        return $client;
    }

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
            $raiseForErrors,
            $this->blockPrivateNetworks
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
            $raiseForErrors,
            $this->blockPrivateNetworks
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
            $ipAddress = null;

            if ($this->blockPrivateNetworks) {
                $ipAddress = $this->resolvePublicIpAddress($url);

                if ($ipAddress === null) {
                    $this->logger->info('HttpClient: blocked request to private network ('.$url.')');

                    if ($raiseForErrors) {
                        throw new ClientException('Blocked URL: '.$url);
                    }

                    return '';
                }

                // Redirect targets are not validated
                $followRedirects = false;
            }

            if (function_exists('curl_version')) {
                if (DEBUG) {
                    $this->logger->debug('HttpClient::doRequest: cURL detected');
                }
                $requestBody = $this->doRequestWithCurl($method, $url, $content, $headers, $raiseForErrors, $followRedirects, $ipAddress);
            } else {
                if (DEBUG) {
                    $this->logger->debug('HttpClient::doRequest: using socket');
                }
                $requestBody = $this->doRequestWithSocket($method, $url, $content, $headers, $raiseForErrors, $followRedirects, $ipAddress);
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
     * @param  string     $ipAddress        Validated IP address to connect to
     * @return string
     */
    private function doRequestWithSocket($method, $url, $content, array $headers, $raiseForErrors = false, $followRedirects = true, $ipAddress = null)
    {
        $startTime = microtime(true);
        $requestUrl = trim($url);
        $peerName = null;

        if ($ipAddress !== null) {
            list($requestUrl, $headers, $peerName) = $this->getPinnedSocketRequest($requestUrl, $headers, $ipAddress);
        }

        $context = $this->getContext($method, $content, $headers, $raiseForErrors, $followRedirects);

        if ($peerName !== null) {
            $context['ssl']['peer_name'] = $peerName;
        }

        $stream = @fopen($requestUrl, 'r', false, stream_context_create($context));

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
     * @param  string     $ipAddress        Validated IP address to connect to
     * @return string
     */
    private function doRequestWithCurl($method, $url, $content, array $headers, $raiseForErrors = false, $followRedirects = true, $ipAddress = null)
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

        if ($ipAddress !== null) {
            curl_setopt($curlSession, CURLOPT_CONNECT_TO, [$this->getCurlConnectTo($ipAddress)]);
            curl_setopt($curlSession, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        }

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
     * Check if an IP address is publicly routable
     *
     * IPv6 addresses embedding an IPv4 address (IPv4-mapped, IPv4-compatible,
     * NAT64 and 6to4) are checked against the embedded IPv4 address.
     *
     * @access public
     * @param  string     $ip
     * @return bool
     */
    public function isPublicIpAddress($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        $packedAddress = inet_pton($ip);

        if ($packedAddress === false) {
            return false;
        }

        if (strlen($packedAddress) === 16) {
            $embeddedAddress = $this->getEmbeddedIPv4Address($packedAddress);

            if ($embeddedAddress !== null) {
                return $this->isPublicIpAddress(inet_ntop($embeddedAddress));
            }

            if (! $this->isPackedAddressInNetworks($packedAddress, ['2000::/3']) ||
                $this->isPackedAddressInNetworks($packedAddress, self::NON_PUBLIC_IPV6_NETWORKS)) {
                return false;
            }
        } elseif ($this->isPackedAddressInNetworks($packedAddress, self::NON_PUBLIC_IPV4_NETWORKS)) {
            return false;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    /**
     * Check if an IP address is private (invalid IP addresses are not considered private)
     *
     * @access public
     * @param  string     $ip
     * @return bool
     */
    public function isPrivateIpAddress($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false && ! $this->isPublicIpAddress($ip);
    }

    /**
     * Check if a URL is private (RFC1918, localhost, etc.)
     *
     * Non HTTP(S) URLs are not considered private. Use withPrivateNetworksBlocked()
     * to enforce the check at connection time.
     *
     * @access public
     * @param  string     $url
     * @return bool
     */
    public function isPrivateURL($url)
    {
        if ($this->getUrlHost($url) === null) {
            return false;
        }

        return $this->resolvePublicIpAddress($url) === null;
    }

    /**
     * Resolve the host of an HTTP(S) URL to a publicly routable IP address
     *
     * Returns null when the URL is not HTTP(S), when the host cannot be resolved,
     * or when any of the resolved addresses is not publicly routable.
     *
     * @access public
     * @param  string     $url
     * @return string|null
     */
    public function resolvePublicIpAddress($url)
    {
        $host = $this->getUrlHost($url);

        if ($host === null) {
            return null;
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            $ipAddresses = [$host];
        } elseif (strpos($host, ':') !== false || $this->isNumericHost($host)) {
            // Invalid IPv6 literal, or alternative IPv4 notation (decimal, octal, hexadecimal, shortened)
            // that libraries interpret differently
            return null;
        } else {
            $ipAddresses = $this->resolveHostname($host);
        }

        if (empty($ipAddresses)) {
            return null;
        }

        foreach ($ipAddresses as $ipAddress) {
            if (! $this->isPublicIpAddress($ipAddress)) {
                return null;
            }
        }

        return $ipAddresses[0];
    }

    /**
     * Resolve a hostname to its IPv4 and IPv6 addresses
     *
     * @access protected
     * @param  string     $hostname
     * @return string[]
     */
    protected function resolveHostname($hostname)
    {
        $ipAddresses = gethostbynamel($hostname) ?: [];

        if (function_exists('dns_get_record')) {
            $records = @dns_get_record($hostname, DNS_AAAA);

            if (is_array($records)) {
                foreach ($records as $record) {
                    if (isset($record['type'], $record['ipv6']) && $record['type'] === 'AAAA') {
                        $ipAddresses[] = $record['ipv6'];
                    }
                }
            }
        }

        return $ipAddresses;
    }

    /**
     * Get the normalized host of an HTTP(S) URL (without brackets and trailing dot)
     *
     * @access private
     * @param  string     $url
     * @return string|null
     */
    private function getUrlHost($url)
    {
        $components = parse_url(trim($url));

        if (! is_array($components) || ! isset($components['scheme'], $components['host'])) {
            return null;
        }

        if (! in_array(strtolower($components['scheme']), ['http', 'https'], true)) {
            return null;
        }

        $host = rtrim(strtolower(trim($components['host'], " \t\n\r\0\x0B[]")), '.');

        return $host === '' ? null : $host;
    }

    /**
     * Check if a host is meant to be an IPv4 address (its last label is a number)
     *
     * Same rule as the WHATWG URL standard: such hosts are never DNS names.
     *
     * @access private
     * @param  string     $host
     * @return bool
     */
    private function isNumericHost($host)
    {
        $labels = explode('.', $host);
        return preg_match('/^(0x[0-9a-f]*|[0-9]+)$/i', end($labels)) === 1;
    }

    /**
     * Get the IPv4 address embedded in a packed IPv6 address
     *
     * @access private
     * @param  string     $packedAddress
     * @return string|null Packed IPv4 address
     */
    private function getEmbeddedIPv4Address($packedAddress)
    {
        $prefix = substr($packedAddress, 0, 12);

        // IPv4-mapped (::ffff:0:0/96), IPv4-compatible (::/96) and NAT64 (64:ff9b::/96)
        if ($prefix === str_repeat("\0", 10)."\xff\xff" ||
            $prefix === str_repeat("\0", 12) ||
            $prefix === "\x00\x64\xff\x9b".str_repeat("\0", 8)) {
            return substr($packedAddress, 12, 4);
        }

        // 6to4 (2002::/16)
        if (substr($packedAddress, 0, 2) === "\x20\x02") {
            return substr($packedAddress, 2, 4);
        }

        return null;
    }

    /**
     * Check if a packed IP address belongs to any of the given networks (CIDR notation)
     *
     * @access private
     * @param  string     $packedAddress
     * @param  string[]   $networks
     * @return bool
     */
    private function isPackedAddressInNetworks($packedAddress, array $networks)
    {
        foreach ($networks as $network) {
            list($subnet, $prefixLength) = explode('/', $network);
            $packedSubnet = inet_pton($subnet);
            $bytes = intdiv((int) $prefixLength, 8);
            $bits = (int) $prefixLength % 8;

            if (strlen($packedSubnet) !== strlen($packedAddress) || strncmp($packedAddress, $packedSubnet, $bytes) !== 0) {
                continue;
            }

            if ($bits === 0) {
                return true;
            }

            $mask = (0xff << (8 - $bits)) & 0xff;

            if ((ord($packedAddress[$bytes]) & $mask) === (ord($packedSubnet[$bytes]) & $mask)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the cURL "connect to" entry that pins every connection to an IP address
     *
     * The empty host and port match any URL, so cURL never resolves the host itself.
     * The Host header and TLS verification still use the host of the URL.
     *
     * @access private
     * @param  string     $ipAddress
     * @return string
     */
    private function getCurlConnectTo($ipAddress)
    {
        return '::'.(strpos($ipAddress, ':') !== false ? '['.$ipAddress.']' : $ipAddress).':';
    }

    /**
     * Rewrite a socket request to connect to an IP address
     *
     * The host of the URL is replaced by the IP address, the original host is
     * kept for the Host header and for TLS verification (peer name and SNI).
     *
     * @access private
     * @param  string     $url
     * @param  string[]   $headers
     * @param  string     $ipAddress
     * @return array      [url, headers, TLS peer name]
     */
    private function getPinnedSocketRequest($url, array $headers, $ipAddress)
    {
        $components = parse_url($url);
        $port = isset($components['port']) ? ':'.$components['port'] : '';
        $userInfo = '';

        if (isset($components['user'])) {
            $userInfo = $components['user'].(isset($components['pass']) ? ':'.$components['pass'] : '').'@';
        }

        $pinnedUrl = strtolower($components['scheme']).'://'.$userInfo;
        $pinnedUrl .= (strpos($ipAddress, ':') !== false ? '['.$ipAddress.']' : $ipAddress).$port;
        $pinnedUrl .= isset($components['path']) ? $components['path'] : '/';
        $pinnedUrl .= isset($components['query']) ? '?'.$components['query'] : '';

        $headers = array_values(array_filter($headers, function ($header) {
            return stripos(ltrim($header), 'host:') !== 0;
        }));

        $headers[] = 'Host: '.$components['host'].$port;

        return [$pinnedUrl, $headers, rtrim(trim($components['host'], '[]'), '.')];
    }
}
