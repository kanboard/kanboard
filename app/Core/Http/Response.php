<?php

namespace Kanboard\Core\Http;

use Kanboard\Core\Base;
use Kanboard\Core\Csv;

/**
 * Response class
 *
 * @package  http
 * @author   Frederic Guillot
 */
class Response extends Base
{
    private $httpStatusCode = 200;
    private $httpHeaders = array();
    private $httpBody = '';
    private $responseSent = false;

    /**
     * Return true if the response have been sent to the user agent
     *
     * @access public
     * @return bool
     */
    public function isResponseAlreadySent()
    {
        return $this->responseSent;
    }

    /**
     * Set HTTP status code
     *
     * @access public
     * @param  integer $statusCode
     * @return $this
     */
    public function withStatusCode($statusCode)
    {
        $this->httpStatusCode = $statusCode;
        return $this;
    }

    /**
     * Set HTTP header
     *
     * @access public
     * @param  string $header
     * @param  string $value
     * @return $this
     */
    public function withHeader($header, $value)
    {
        $this->httpHeaders[$header] = $value;
        return $this;
    }

    /**
     * Set content type header
     *
     * @access public
     * @param  string $value
     * @return $this
     */
    public function withContentType($value)
    {
        $this->httpHeaders['Content-Type'] = $value;
        return $this;
    }

    /**
     * Set default security headers
     *
     * @access public
     * @return $this
     */
    public function withSecurityHeaders()
    {
        $this->httpHeaders['X-Content-Type-Options'] = 'nosniff';
        $this->httpHeaders['X-XSS-Protection'] = '1; mode=block';
        return $this;
    }

    /**
     * Set header Content-Security-Policy
     *
     * @access public
     * @param  array  $policies
     * @return $this
     */
    public function withContentSecurityPolicy(array $policies = array())
    {
        $values = '';

        foreach ($policies as $policy => $acl) {
            $values .= $policy.' '.trim($acl).'; ';
        }

        $this->withHeader('Content-Security-Policy', $values);
        return $this;
    }

    /**
     * Set header X-Frame-Options
     *
     * @access public
     * @return $this
     */
    public function withXframe()
    {
        $this->withHeader('X-Frame-Options', 'DENY');
        return $this;
    }

    /**
     * Set header Strict-Transport-Security (only if we use HTTPS)
     *
     * @access public
     * @return $this
     */
    public function withStrictTransportSecurity()
    {
        if ($this->request->isHTTPS()) {
            $this->withHeader('Strict-Transport-Security', 'max-age=31536000');
        }

        return $this;
    }

    /**
     * Add P3P headers for Internet Explorer
     *
     * @access public
     * @return $this
     */
    public function withP3P()
    {
        $this->withHeader('P3P', 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        return $this;
    }

    /**
     * Set HTTP response body
     *
     * @access public
     * @param  string $body
     * @return $this
     */
    public function withBody($body)
    {
        $this->httpBody = $body;
        return $this;
    }

    /**
     * Send headers to cache a resource
     *
     * @access public
     * @param  integer $duration
     * @param  string  $etag
     * @return $this
     */
    public function withCache($duration, $etag = '')
    {
        $this
            ->withHeader('Pragma', 'cache')
            ->withHeader('Expires', gmdate('D, d M Y H:i:s', time() + $duration) . ' GMT')
            ->withHeader('Cache-Control', 'public, max-age=' . $duration)
        ;

        if ($etag) {
            $this->withHeader('ETag', '"' . $etag . '"');
        }

        return $this;
    }

    /**
     * Send no cache headers
     *
     * @access public
     * @return $this
     */
    public function withoutCache()
    {
        $this->withHeader('Pragma', 'no-cache');
        $this->withHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
        return $this;
    }

    /**
     * Force the browser to download an attachment
     *
     * @access public
     * @param  string $filename
     * @return $this
     */
    public function withFileDownload($filename)
    {
        $this->withHeader('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $this->withHeader('Content-Transfer-Encoding', 'binary');
        $this->withHeader('Content-Type', 'application/octet-stream');
        return $this;
    }

    /**
     * Send headers and body
     *
     * @access public
     */
    public function send()
    {
        $this->responseSent = true;

        if ($this->httpStatusCode !== 200) {
            header('Status: '.$this->httpStatusCode);
            header($this->request->getServerVariable('SERVER_PROTOCOL').' '.$this->httpStatusCode);
        }

        foreach ($this->httpHeaders as $header => $value) {
            header($header.': '.$value);
        }

        if (! empty($this->httpBody)) {
            echo $this->httpBody;
        }
    }

    /**
     * Send a custom HTTP status code
     *
     * @access public
     * @param  integer $statusCode
     */
    public function status($statusCode)
    {
        $this->withStatusCode($statusCode);
        $this->send();
    }

    /**
     * Redirect to another URL
     *
     * @access public
     * @param  string   $url   Redirection URL
     * @param  boolean  $self  If Ajax request and true: refresh the current page
     */
    public function redirect($url, $self = false)
    {
        if ($this->request->isAjax()) {
            $this->withHeader('X-Ajax-Redirect', $self ? 'self' : $url);
        } else {
            $this->withHeader('Location', $url);
        }

        $this->send();
    }

    /**
     * Send a HTML response
     *
     * @access public
     * @param  string  $data
     * @param  integer $statusCode
     */
    public function html($data, $statusCode = 200)
    {
        $this->withStatusCode($statusCode);
        $this->withContentType('text/html; charset=utf-8');
        $this->withBody($data);
        $this->send();
    }

    /**
     * Send a text response
     *
     * @access public
     * @param  string   $data
     * @param  integer  $statusCode
     */
    public function text($data, $statusCode = 200)
    {
        $this->withStatusCode($statusCode);
        $this->withContentType('text/plain; charset=utf-8');
        $this->withBody($data);
        $this->send();
    }

    /**
     * Send a CSV response
     *
     * @access public
     * @param  array  $data  Data to serialize in csv
     */
    public function csv(array $data)
    {
        $this->withoutCache();
        $this->withContentType('text/csv; charset=utf-8');
        $this->send();
        Csv::output($data);
    }

    /**
     * Send a Json response
     *
     * @access public
     * @param  array    $data         Data to serialize in json
     * @param  integer  $statusCode   HTTP status code
     */
    public function json(array $data, $statusCode = 200)
    {
        $this->withStatusCode($statusCode);
        $this->withContentType('application/json');
        $this->withoutCache();
        $this->withBody(json_encode($data));
        $this->send();
    }

    /**
     * Send a XML response
     *
     * @access public
     * @param  string   $data
     * @param  integer  $statusCode
     */
    public function xml($data, $statusCode = 200)
    {
        $this->withStatusCode($statusCode);
        $this->withContentType('text/xml; charset=utf-8');
        $this->withoutCache();
        $this->withBody($data);
        $this->send();
    }

    /**
     * Send a javascript response
     *
     * @access public
     * @param  string  $data
     * @param  integer $statusCode
     */
    public function js($data, $statusCode = 200)
    {
        $this->withStatusCode($statusCode);
        $this->withContentType('text/javascript; charset=utf-8');
        $this->withBody($data);
        $this->send();
    }

    /**
     * Send a css response
     *
     * @access public
     * @param  string  $data
     * @param  integer $statusCode
     */
    public function css($data, $statusCode = 200)
    {
        $this->withStatusCode($statusCode);
        $this->withContentType('text/css; charset=utf-8');
        $this->withBody($data);
        $this->send();
    }

    /**
     * Send a binary response
     *
     * @access public
     * @param  string  $data
     * @param  integer $statusCode
     */
    public function binary($data, $statusCode = 200)
    {
        $this->withStatusCode($statusCode);
        $this->withoutCache();
        $this->withHeader('Content-Transfer-Encoding', 'binary');
        $this->withContentType('application/octet-stream');
        $this->withBody($data);
        $this->send();
    }

    /**
     * Send a iCal response
     *
     * @access public
     * @param  string  $data
     * @param  integer $statusCode
     */
    public function ical($data, $statusCode = 200)
    {
        $this->withStatusCode($statusCode);
        $this->withContentType('text/calendar; charset=utf-8');
        $this->withBody($data);
        $this->send();
    }
}
