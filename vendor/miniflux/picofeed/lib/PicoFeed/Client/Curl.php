<?php

namespace PicoFeed\Client;

use PicoFeed\Logging\Logger;

/**
 * cURL HTTP client.
 *
 * @author  Frederic Guillot
 */
class Curl extends Client
{
    protected $nbRedirects = 0;

    /**
     * HTTP response body.
     *
     * @var string
     */
    private $body = '';

    /**
     * Body size.
     *
     * @var int
     */
    private $body_length = 0;

    /**
     * HTTP response headers.
     *
     * @var array
     */
    private $response_headers = array();

    /**
     * Counter on the number of header received.
     *
     * @var int
     */
    private $response_headers_count = 0;

    /**
     * cURL callback to read the HTTP body.
     *
     * If the function return -1, curl stop to read the HTTP response
     *
     * @param resource $ch     cURL handler
     * @param string   $buffer Chunk of data
     *
     * @return int Length of the buffer
     */
    public function readBody($ch, $buffer)
    {
        $length = strlen($buffer);
        $this->body_length += $length;

        if ($this->body_length > $this->max_body_size) {
            return -1;
        }

        $this->body .= $buffer;

        return $length;
    }

    /**
     * cURL callback to read HTTP headers.
     *
     * @param resource $ch     cURL handler
     * @param string   $buffer Header line
     *
     * @return int Length of the buffer
     */
    public function readHeaders($ch, $buffer)
    {
        $length = strlen($buffer);

        if ($buffer === "\r\n" || $buffer === "\n") {
            ++$this->response_headers_count;
        } else {
            if (!isset($this->response_headers[$this->response_headers_count])) {
                $this->response_headers[$this->response_headers_count] = '';
            }

            $this->response_headers[$this->response_headers_count] .= $buffer;
        }

        return $length;
    }

    /**
     * cURL callback to passthrough the HTTP body to the client.
     *
     * If the function return -1, curl stop to read the HTTP response
     *
     * @param resource $ch     cURL handler
     * @param string   $buffer Chunk of data
     *
     * @return int Length of the buffer
     */
    public function passthroughBody($ch, $buffer)
    {
        // do it only at the beginning of a transmission
        if ($this->body_length === 0) {
            list($status, $headers) = HttpHeaders::parse(explode("\n", $this->response_headers[$this->response_headers_count - 1]));

            if ($this->isRedirection($status)) {
                return $this->handleRedirection($headers['Location']);
            }

            // Do not work with PHP-FPM
            if (strpos(PHP_SAPI, 'cgi') !== false) {
                header(':', true, $status);
            }

            if (isset($headers['Content-Type'])) {
                header('Content-Type:' .$headers['Content-Type']);
            }
        }

        $length = strlen($buffer);
        $this->body_length += $length;

        echo $buffer;

        return $length;
    }

    /**
     * Prepare HTTP headers.
     *
     * @return string[]
     */
    private function prepareHeaders()
    {
        $headers = array(
            'Connection: close',
        );

        if ($this->etag) {
            $headers[] = 'If-None-Match: '.$this->etag;
            $headers[] = 'A-IM: feed';
        }

        if ($this->last_modified) {
            $headers[] = 'If-Modified-Since: '.$this->last_modified;
        }

        $headers = array_merge($headers, $this->request_headers);

        return $headers;
    }

    /**
     * Prepare curl proxy context.
     *
     * @param resource $ch
     *
     * @return resource $ch
     */
    private function prepareProxyContext($ch)
    {
        if ($this->proxy_hostname) {
            Logger::setMessage(get_called_class().' Proxy: '.$this->proxy_hostname.':'.$this->proxy_port);

            curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy_port);
            curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy_hostname);

            if ($this->proxy_username) {
                Logger::setMessage(get_called_class().' Proxy credentials: Yes');
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_username.':'.$this->proxy_password);
            } else {
                Logger::setMessage(get_called_class().' Proxy credentials: No');
            }
        }

        return $ch;
    }

    /**
     * Prepare curl auth context.
     *
     * @param resource $ch
     *
     * @return resource $ch
     */
    private function prepareAuthContext($ch)
    {
        if ($this->username && $this->password) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
        }

        return $ch;
    }

    /**
     * Set write/header functions.
     *
     * @param resource $ch
     *
     * @return resource $ch
     */
    private function prepareDownloadMode($ch)
    {
        $this->body = '';
        $this->response_headers = array();
        $this->response_headers_count = 0;
        $write_function = 'readBody';
        $header_function = 'readHeaders';

        if ($this->isPassthroughEnabled()) {
            $write_function = 'passthroughBody';
        }

        curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, $write_function));
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, $header_function));

        return $ch;
    }

    /**
     * Prepare curl context.
     *
     * @return resource
     */
    private function prepareContext()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->prepareHeaders());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'php://memory');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'php://memory');

        // Disable SSLv3 by enforcing TLSv1.x for curl >= 7.34.0 and < 7.39.0.
        // Versions prior to 7.34 and at least when compiled against openssl
        // interpret this parameter as "limit to TLSv1.0" which fails for sites
        // which enforce TLS 1.1+.
        // Starting with curl 7.39.0 SSLv3 is disabled by default.
        $version = curl_version();
        if ($version['version_number'] >= 467456 && $version['version_number'] < 468736) {
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        }

        $ch = $this->prepareDownloadMode($ch);
        $ch = $this->prepareProxyContext($ch);
        $ch = $this->prepareAuthContext($ch);

        return $ch;
    }

    /**
     * Execute curl context.
     */
    private function executeContext()
    {
        $ch = $this->prepareContext();
        curl_exec($ch);

        Logger::setMessage(get_called_class().' cURL total time: '.curl_getinfo($ch, CURLINFO_TOTAL_TIME));
        Logger::setMessage(get_called_class().' cURL dns lookup time: '.curl_getinfo($ch, CURLINFO_NAMELOOKUP_TIME));
        Logger::setMessage(get_called_class().' cURL connect time: '.curl_getinfo($ch, CURLINFO_CONNECT_TIME));
        Logger::setMessage(get_called_class().' cURL speed download: '.curl_getinfo($ch, CURLINFO_SPEED_DOWNLOAD));
        Logger::setMessage(get_called_class().' cURL effective url: '.curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));

        $curl_errno = curl_errno($ch);

        if ($curl_errno) {
            Logger::setMessage(get_called_class().' cURL error: '.curl_error($ch));
            curl_close($ch);

            $this->handleError($curl_errno);
        }

        // Update the url if there where redirects
        $this->url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        curl_close($ch);
    }

    /**
     * Do the HTTP request.
     *
     * @return array HTTP response ['body' => ..., 'status' => ..., 'headers' => ...]
     */
    public function doRequest()
    {
        $this->executeContext();

        list($status, $headers) = HttpHeaders::parse(explode("\n", $this->response_headers[$this->response_headers_count - 1]));

        if ($this->isRedirection($status)) {
            if (empty($headers['Location'])) {
                $status = 200;
            } else {
                return $this->handleRedirection($headers['Location']);
            }
        }

        return array(
            'status' => $status,
            'body' => $this->body,
            'headers' => $headers,
        );
    }

    /**
     * Handle HTTP redirects
     *
     * @param string $location Redirected URL
     * @return array
     * @throws MaxRedirectException
     */
    private function handleRedirection($location)
    {
        $result = array();
        $this->url = Url::resolve($location, $this->url);
        $this->body = '';
        $this->body_length = 0;
        $this->response_headers = array();
        $this->response_headers_count = 0;

        while (true) {
            $this->nbRedirects++;

            if ($this->nbRedirects >= $this->max_redirects) {
                throw new MaxRedirectException('Maximum number of redirections reached');
            }

            $result = $this->doRequest();

            if ($this->isRedirection($result['status'])) {
                $this->url = Url::resolve($result['headers']['Location'], $this->url);
                $this->body = '';
                $this->body_length = 0;
                $this->response_headers = array();
                $this->response_headers_count = 0;
            } else {
                break;
            }
        }

        return $result;
    }

    /**
     * Handle cURL errors (throw individual exceptions).
     *
     * We don't use constants because they are not necessary always available
     * (depends of the version of libcurl linked to php)
     *
     * @see    http://curl.haxx.se/libcurl/c/libcurl-errors.html
     *
     * @param int $errno cURL error code
     * @throws InvalidCertificateException
     * @throws InvalidUrlException
     * @throws MaxRedirectException
     * @throws MaxSizeException
     * @throws TimeoutException
     */
    private function handleError($errno)
    {
        switch ($errno) {
            case 78: // CURLE_REMOTE_FILE_NOT_FOUND
                throw new InvalidUrlException('Resource not found', $errno);
            case 6:  // CURLE_COULDNT_RESOLVE_HOST
                throw new InvalidUrlException('Unable to resolve hostname', $errno);
            case 7:  // CURLE_COULDNT_CONNECT
                throw new InvalidUrlException('Unable to connect to the remote host', $errno);
            case 23: // CURLE_WRITE_ERROR
                throw new MaxSizeException('Maximum response size exceeded', $errno);
            case 28: // CURLE_OPERATION_TIMEDOUT
                throw new TimeoutException('Operation timeout', $errno);
            case 35: // CURLE_SSL_CONNECT_ERROR
            case 51: // CURLE_PEER_FAILED_VERIFICATION
            case 58: // CURLE_SSL_CERTPROBLEM
            case 60: // CURLE_SSL_CACERT
            case 59: // CURLE_SSL_CIPHER
            case 64: // CURLE_USE_SSL_FAILED
            case 66: // CURLE_SSL_ENGINE_INITFAILED
            case 77: // CURLE_SSL_CACERT_BADFILE
            case 83: // CURLE_SSL_ISSUER_ERROR
                $msg = 'Invalid SSL certificate caused by CURL error number ' . $errno;
                throw new InvalidCertificateException($msg, $errno);
            case 47: // CURLE_TOO_MANY_REDIRECTS
                throw new MaxRedirectException('Maximum number of redirections reached', $errno);
            case 63: // CURLE_FILESIZE_EXCEEDED
                throw new MaxSizeException('Maximum response size exceeded', $errno);
            default:
                throw new InvalidUrlException('Unable to fetch the URL', $errno);
        }
    }
}
