<?php

namespace JsonRPC;

use BadFunctionCallException;

/**
 * JsonRPC client class
 *
 * @package JsonRPC
 * @author Frederic Guillot
 * @license Unlicense http://unlicense.org/
 */
class Client
{
    /**
     * URL of the server
     *
     * @access private
     * @var string
     */
    private $url;

    /**
     * HTTP client timeout
     *
     * @access private
     * @var integer
     */
    private $timeout;

    /**
     * Username for authentication
     *
     * @access private
     * @var string
     */
    private $username;

    /**
     * Password for authentication
     *
     * @access private
     * @var string
     */
    private $password;

    /**
     * Enable debug output to the php error log
     *
     * @access public
     * @var boolean
     */
    public $debug = false;

    /**
     * Default HTTP headers to send to the server
     *
     * @access private
     * @var array
     */
    private $headers = array(
        'Connection: close',
        'Content-Type: application/json',
        'Accept: application/json'
    );

    /**
     * Constructor
     *
     * @access public
     * @param  string    $url         Server URL
     * @param  integer   $timeout     Server URL
     * @param  array     $headers     Custom HTTP headers
     */
    public function __construct($url, $timeout = 5, $headers = array())
    {
        $this->url = $url;
        $this->timeout = $timeout;
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Automatic mapping of procedures
     *
     * @access public
     * @param  string   $method   Procedure name
     * @param  array    $params   Procedure arguments
     * @return mixed
     */
    public function __call($method, $params)
    {
        return $this->execute($method, $params);
    }

    /**
     * Set authentication parameters
     *
     * @access public
     * @param  string   $username   Username
     * @param  string   $password   Password
     */
    public function authentication($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Execute
     *
     * @access public
     * @throws BadFunctionCallException  Exception thrown when a bad request is made (missing argument/procedure)
     * @param  string   $procedure   Procedure name
     * @param  array    $params      Procedure arguments
     * @return mixed
     */
    public function execute($procedure, array $params = array())
    {
        $id = mt_rand();

        $payload = array(
            'jsonrpc' => '2.0',
            'method' => $procedure,
            'id' => $id
        );

        if (! empty($params)) {
            $payload['params'] = $params;
        }

        $result = $this->doRequest($payload);

        if (isset($result['id']) && $result['id'] == $id && array_key_exists('result', $result)) {
            return $result['result'];
        }

        throw new BadFunctionCallException('Bad Request');
    }

    /**
     * Do the HTTP request
     *
     * @access public
     * @param  string   $payload   Data to send
     */
    public function doRequest($payload)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JSON-RPC PHP Client');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        if ($this->username && $this->password) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
        }

        $result = curl_exec($ch);
        $response = json_decode($result, true);

        if ($this->debug) {
            error_log('==> Request: '.PHP_EOL.json_encode($payload, JSON_PRETTY_PRINT));
            error_log('==> Response: '.PHP_EOL.json_encode($response, JSON_PRETTY_PRINT));
        }

        curl_close($ch);

        return is_array($response) ? $response : array();
    }
}
