<?php

namespace Core;

class Response
{
    public function forceDownload($filename)
    {
        header('Content-Disposition: attachment; filename="'.$filename.'"');
    }

    /**
     * @param integer $status_code
     */
    public function status($status_code)
    {
        if (strpos(php_sapi_name(), 'apache') !== false) {
            header('HTTP/1.0 '.$status_code);
        }
        else {
            header('Status: '.$status_code);
        }
    }

    public function redirect($url)
    {
        header('Location: '.$url);
        exit;
    }

    public function json(array $data, $status_code = 200)
    {
        $this->status($status_code);

        header('Content-Type: application/json');
        echo json_encode($data);

        exit;
    }

    public function text($data, $status_code = 200)
    {
        $this->status($status_code);

        header('Content-Type: text/plain; charset=utf-8');
        echo $data;

        exit;
    }

    public function html($data, $status_code = 200)
    {
        $this->status($status_code);

        header('Content-Type: text/html; charset=utf-8');
        echo $data;

        exit;
    }

    public function xml($data, $status_code = 200)
    {
        $this->status($status_code);

        header('Content-Type: text/xml; charset=utf-8');
        echo $data;

        exit;
    }

    public function js($data, $status_code = 200)
    {
        $this->status($status_code);

        header('Content-Type: text/javascript; charset=utf-8');
        echo $data;

        exit;
    }

    public function binary($data, $status_code = 200)
    {
        $this->status($status_code);

        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/octet-stream');
        echo $data;

        exit;
    }

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

    public function nosniff()
    {
        header('X-Content-Type-Options: nosniff');
    }

    public function xss()
    {
        header('X-XSS-Protection: 1; mode=block');
    }

    public function hsts()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            header('Strict-Transport-Security: max-age=31536000');
        }
    }

    public function xframe($mode = 'DENY', array $urls = array())
    {
        header('X-Frame-Options: '.$mode.' '.implode(' ', $urls));
    }
}
