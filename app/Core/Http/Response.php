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
    /**
     * Send headers to cache a resource
     *
     * @access public
     * @param  integer $duration
     * @param  string  $etag
     */
    public function cache($duration, $etag = '')
    {
        header('Pragma: cache');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $duration) . ' GMT');
        header('Cache-Control: public, max-age=' . $duration);

        if ($etag) {
            header('ETag: "' . $etag . '"');
        }
    }

    /**
     * Send no cache headers
     *
     * @access public
     */
    public function nocache()
    {
        header('Pragma: no-cache');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        // Use no-store due to a Chrome bug: https://code.google.com/p/chromium/issues/detail?id=28035
        header('Cache-Control: no-store, must-revalidate');
    }

    /**
     * Send a custom Content-Type header
     *
     * @access public
     * @param  string   $mimetype   Mime-type
     */
    public function contentType($mimetype)
    {
        header('Content-Type: '.$mimetype);
    }

    /**
     * Force the browser to download an attachment
     *
     * @access public
     * @param  string   $filename    File name
     */
    public function forceDownload($filename)
    {
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/octet-stream');
    }

    /**
     * Send a custom HTTP status code
     *
     * @access public
     * @param  integer   $status_code   HTTP status code
     */
    public function status($status_code)
    {
        header('Status: '.$status_code);
        header($this->request->getServerVariable('SERVER_PROTOCOL').' '.$status_code);
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
            header('X-Ajax-Redirect: '.($self ? 'self' : $url));
        } else {
            header('Location: '.$url);
        }

        exit;
    }

    /**
     * Send a CSV response
     *
     * @access public
     * @param  array    $data          Data to serialize in csv
     * @param  integer  $status_code   HTTP status code
     */
    public function csv(array $data, $status_code = 200)
    {
        $this->status($status_code);
        $this->nocache();

        header('Content-Type: text/csv');
        Csv::output($data);
        exit;
    }

    /**
     * Send a Json response
     *
     * @access public
     * @param  array    $data          Data to serialize in json
     * @param  integer  $status_code   HTTP status code
     */
    public function json(array $data, $status_code = 200)
    {
        $this->status($status_code);
        $this->nocache();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Send a text response
     *
     * @access public
     * @param  string   $data          Raw data
     * @param  integer  $status_code   HTTP status code
     */
    public function text($data, $status_code = 200)
    {
        $this->status($status_code);
        $this->nocache();
        header('Content-Type: text/plain; charset=utf-8');
        echo $data;
        exit;
    }

    /**
     * Send a HTML response
     *
     * @access public
     * @param  string   $data          Raw data
     * @param  integer  $status_code   HTTP status code
     */
    public function html($data, $status_code = 200)
    {
        $this->status($status_code);
        $this->nocache();
        header('Content-Type: text/html; charset=utf-8');
        echo $data;
        exit;
    }

    /**
     * Send a XML response
     *
     * @access public
     * @param  string   $data          Raw data
     * @param  integer  $status_code   HTTP status code
     */
    public function xml($data, $status_code = 200)
    {
        $this->status($status_code);
        $this->nocache();
        header('Content-Type: text/xml; charset=utf-8');
        echo $data;
        exit;
    }

    /**
     * Send a javascript response
     *
     * @access public
     * @param  string   $data          Raw data
     * @param  integer  $status_code   HTTP status code
     */
    public function js($data, $status_code = 200)
    {
        $this->status($status_code);

        header('Content-Type: text/javascript; charset=utf-8');
        echo $data;

        exit;
    }

    /**
     * Send a css response
     *
     * @access public
     * @param  string   $data          Raw data
     * @param  integer  $status_code   HTTP status code
     */
    public function css($data, $status_code = 200)
    {
        $this->status($status_code);

        header('Content-Type: text/css; charset=utf-8');
        echo $data;

        exit;
    }

    /**
     * Send a binary response
     *
     * @access public
     * @param  string   $data          Raw data
     * @param  integer  $status_code   HTTP status code
     */
    public function binary($data, $status_code = 200)
    {
        $this->status($status_code);
        $this->nocache();
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/octet-stream');
        echo $data;
        exit;
    }

    /**
     * Send the security header: Content-Security-Policy
     *
     * @access public
     * @param  array    $policies   CSP rules
     */
    public function csp(array $policies = array())
    {
        $values = '';

        foreach ($policies as $policy => $acl) {
            $values .= $policy.' '.trim($acl).'; ';
        }

        header('Content-Security-Policy: '.$values);
    }

    /**
     * Send the security header: X-Content-Type-Options
     *
     * @access public
     */
    public function nosniff()
    {
        header('X-Content-Type-Options: nosniff');
    }

    /**
     * Send the security header: X-XSS-Protection
     *
     * @access public
     */
    public function xss()
    {
        header('X-XSS-Protection: 1; mode=block');
    }

    /**
     * Send the security header: Strict-Transport-Security (only if we use HTTPS)
     *
     * @access public
     */
    public function hsts()
    {
        if ($this->request->isHTTPS()) {
            header('Strict-Transport-Security: max-age=31536000');
        }
    }

    /**
     * Send the security header: X-Frame-Options (deny by default)
     *
     * @access public
     * @param  string   $mode   Frame option mode
     * @param  array    $urls   Allowed urls for the given mode
     */
    public function xframe($mode = 'DENY', array $urls = array())
    {
        header('X-Frame-Options: '.$mode.' '.implode(' ', $urls));
    }
}
