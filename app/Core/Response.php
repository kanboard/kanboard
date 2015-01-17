<?php

namespace Core;

/**
 * Response class
 *
 * @package  core
 * @author   Frederic Guillot
 */
class Response
{
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
        header($_SERVER['SERVER_PROTOCOL'].' '.$status_code);
    }

    /**
     * Redirect to another URL
     *
     * @access public
     * @param  string   $url   Redirection URL
     */
    public function redirect($url)
    {
        header('Location: '.$url);
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
        Tool::csv($data);
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
        $policies['default-src'] = "'self'";
        $values = '';

        foreach ($policies as $policy => $hosts) {

            if (is_array($hosts)) {

                $acl = '';

                foreach ($hosts as &$host) {

                    if ($host === '*' || $host === 'self' || strpos($host, 'http') === 0) {
                        $acl .= $host.' ';
                    }
                }
            }
            else {

                $acl = $hosts;
            }

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
        if (Request::isHTTPS()) {
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
