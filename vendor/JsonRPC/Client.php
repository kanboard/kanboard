<?php

namespace JsonRPC;

/**
 * JsonRPC client class
 *
 * @package JsonRPC
 * @author Frderic Guillot
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

        return null;
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

        curl_close($ch);

        return is_array($response) ? $response : array();
    }
}
