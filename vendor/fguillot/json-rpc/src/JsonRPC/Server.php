<?php

namespace JsonRPC;

use Closure;
use BadFunctionCallException;
use Exception;
use InvalidArgumentException;
use LogicException;
use ReflectionFunction;
use ReflectionMethod;

class InvalidJsonRpcFormat extends Exception {};
class InvalidJsonFormat extends Exception {};
class AuthenticationFailure extends Exception {};

/**
 * JsonRPC server class
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class Server
{
    /**
     * Data received from the client
     *
     * @access private
     * @var array
     */
    private $payload = array();

    /**
     * List of procedures
     *
     * @access private
     * @var array
     */
    private $callbacks = array();

    /**
     * List of classes
     *
     * @access private
     * @var array
     */
    private $classes = array();

    /**
     * List of instances
     *
     * @access private
     * @var array
     */
    private $instances = array();

    /**
     * List of exception classes that should be relayed to client
     *
     * @access private
     * @var array
     */
    private $exceptions = array();

    /**
     * Method name to execute before the procedure
     *
     * @access private
     * @var string
     */
    private $before = '';

    /**
     * Username
     *
     * @access private
     * @var string
     */
    private $username = '';

    /**
     * Password
     *
     * @access private
     * @var string
     */
    private $password = '';

    /**
     * Constructor
     *
     * @access public
     * @param  string    $request
     */
    public function __construct($request = '')
    {
        if ($request !== '') {
            $this->payload = json_decode($request, true);
        }
        else {
            $this->payload = json_decode(file_get_contents('php://input'), true);
        }
    }

    /**
     * Set a payload
     *
     * @access public
     * @param  array   $payload
     * @return Server
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Define alternative authentication header
     *
     * @access public
     * @param  string   $header   Header name
     * @return Server
     */
    public function setAuthenticationHeader($header)
    {
        if (! empty($header)) {

            $header = 'HTTP_'.str_replace('-', '_', strtoupper($header));

            if (isset($_SERVER[$header])) {
                list($this->username, $this->password) = explode(':', @base64_decode($_SERVER[$header]));
            }
        }

        return $this;
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return $this->username ?: @$_SERVER['PHP_AUTH_USER'];
    }

    /**
     * Get password
     *
     * @access public
     * @return string
     */
    public function getPassword()
    {
        return $this->password ?: @$_SERVER['PHP_AUTH_PW'];
    }

    /**
     * Send authentication failure response
     *
     * @access public
     */
    public function sendAuthenticationFailureResponse()
    {
        header('WWW-Authenticate: Basic realm="JsonRPC"');
        header('Content-Type: application/json');
        header('HTTP/1.0 401 Unauthorized');
        echo '{"error": "Authentication failed"}';
        exit;
    }

    /**
     * Send forbidden response
     *
     * @access public
     */
    public function sendForbiddenResponse()
    {
        header('Content-Type: application/json');
        header('HTTP/1.0 403 Forbidden');
        echo '{"error": "Access Forbidden"}';
        exit;
    }

    /**
     * IP based client restrictions
     *
     * Return an HTTP error 403 if the client is not allowed
     *
     * @access public
     * @param  array   $hosts   List of hosts
     */
    public function allowHosts(array $hosts)
    {
        if (! in_array($_SERVER['REMOTE_ADDR'], $hosts)) {
            $this->sendForbiddenResponse();
        }
    }

    /**
     * HTTP Basic authentication
     *
     * Return an HTTP error 401 if the client is not allowed
     *
     * @access public
     * @param  array   $users   Map of username/password
     * @return Server
     */
    public function authentication(array $users)
    {
        if (! isset($users[$this->getUsername()]) || $users[$this->getUsername()] !== $this->getPassword()) {
            $this->sendAuthenticationFailureResponse();
        }

        return $this;
    }

    /**
     * Register a new procedure
     *
     * @access public
     * @param  string   $procedure       Procedure name
     * @param  closure  $callback        Callback
     * @return Server
     */
    public function register($procedure, Closure $callback)
    {
        $this->callbacks[$procedure] = $callback;
        return $this;
    }

    /**
     * Bind a procedure to a class
     *
     * @access public
     * @param  string   $procedure    Procedure name
     * @param  mixed    $class        Class name or instance
     * @param  string   $method       Procedure name
     * @return Server
     */
    public function bind($procedure, $class, $method = '')
    {
        if ($method === '') {
            $method = $procedure;
        }

        $this->classes[$procedure] = array($class, $method);
        return $this;
    }

    /**
     * Bind a class instance
     *
     * @access public
     * @param  mixed   $instance    Instance name
     * @return Server
     */
    public function attach($instance)
    {
        $this->instances[] = $instance;
        return $this;
    }

    /**
     * Bind an exception
     * If this exception occurs it is relayed to the client as JSON-RPC error
     *
     * @access public
     * @param  mixed   $exception    Exception class. Defaults to all.
     * @return Server
     */
    public function attachException($exception = 'Exception')
    {
        $this->exceptions[] = $exception;
        return $this;
    }

    /**
     * Attach a method that will be called before the procedure
     *
     * @access public
     * @param  string  $before
     * @return Server
     */
    public function before($before)
    {
        $this->before = $before;
        return $this;
    }

    /**
     * Return the response to the client
     *
     * @access public
     * @param  array    $data      Data to send to the client
     * @param  array    $payload   Incoming data
     * @return string
     */
    public function getResponse(array $data, array $payload = array())
    {
        if (! array_key_exists('id', $payload)) {
            return '';
        }

        $response = array(
            'jsonrpc' => '2.0',
            'id' => $payload['id']
        );

        $response = array_merge($response, $data);

        @header('Content-Type: application/json');
        return json_encode($response);
    }

    /**
     * Parse the payload and test if the parsed JSON is ok
     *
     * @access private
     */
    private function checkJsonFormat()
    {
        if (! is_array($this->payload)) {
            throw new InvalidJsonFormat('Malformed payload');
        }
    }

    /**
     * Test if all required JSON-RPC parameters are here
     *
     * @access private
     */
    private function checkRpcFormat()
    {
        if (! isset($this->payload['jsonrpc']) ||
            ! isset($this->payload['method']) ||
            ! is_string($this->payload['method']) ||
            $this->payload['jsonrpc'] !== '2.0' ||
            (isset($this->payload['params']) && ! is_array($this->payload['params']))) {

            throw new InvalidJsonRpcFormat('Invalid JSON RPC payload');
        }
    }

    /**
     * Return true if we have a batch request
     *
     * @access public
     * @return boolean
     */
    private function isBatchRequest()
    {
        return array_keys($this->payload) === range(0, count($this->payload) - 1);
    }

    /**
     * Handle batch request
     *
     * @access private
     * @return string
     */
    private function handleBatchRequest()
    {
        $responses = array();

        foreach ($this->payload as $payload) {

            if (! is_array($payload)) {

                $responses[] = $this->getResponse(array(
                    'error' => array(
                        'code' => -32600,
                        'message' => 'Invalid Request'
                    )),
                    array('id' => null)
                );
            }
            else {

                $server = clone($this);
                $server->setPayload($payload);
                $response = $server->execute();

                if (! empty($response)) {
                    $responses[] = $response;
                }
            }
        }

        return empty($responses) ? '' : '['.implode(',', $responses).']';
    }

    /**
     * Parse incoming requests
     *
     * @access public
     * @return string
     */
    public function execute()
    {
        try {

            $this->checkJsonFormat();

            if ($this->isBatchRequest()){
                return $this->handleBatchRequest();
            }

            $this->checkRpcFormat();

            $result = $this->executeProcedure(
                $this->payload['method'],
                empty($this->payload['params']) ? array() : $this->payload['params']
            );

            return $this->getResponse(array('result' => $result), $this->payload);
        }
        catch (InvalidJsonFormat $e) {

            return $this->getResponse(array(
                'error' => array(
                    'code' => -32700,
                    'message' => 'Parse error'
                )),
                array('id' => null)
            );
        }
        catch (InvalidJsonRpcFormat $e) {

            return $this->getResponse(array(
                'error' => array(
                    'code' => -32600,
                    'message' => 'Invalid Request'
                )),
                array('id' => null)
            );
        }
        catch (BadFunctionCallException $e) {

            return $this->getResponse(array(
                'error' => array(
                    'code' => -32601,
                    'message' => 'Method not found'
                )),
                $this->payload
            );
        }
        catch (InvalidArgumentException $e) {

            return $this->getResponse(array(
                'error' => array(
                    'code' => -32602,
                    'message' => 'Invalid params'
                )),
                $this->payload
            );
        }
        catch (AuthenticationFailure $e) {
            $this->sendAuthenticationFailureResponse();
        }
        catch (AccessDeniedException $e) {
            $this->sendForbiddenResponse();
        }
        catch (ResponseException $e) {
            return $this->getResponse(array(
                'error' => array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'data' => $e->getData(),
                )),
                $this->payload
            );
        }
        catch (Exception $e) {

            foreach ($this->exceptions as $class) {
                if ($e instanceof $class) {
                    return $this->getResponse(array(
                        'error' => array(
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        )),
                        $this->payload
                    );
                }
            }

            throw $e;
        }
    }

    /**
     * Execute the procedure
     *
     * @access public
     * @param  string   $procedure    Procedure name
     * @param  array    $params       Procedure params
     * @return mixed
     */
    public function executeProcedure($procedure, array $params = array())
    {
        if (isset($this->callbacks[$procedure])) {
            return $this->executeCallback($this->callbacks[$procedure], $params);
        }
        else if (isset($this->classes[$procedure]) && method_exists($this->classes[$procedure][0], $this->classes[$procedure][1])) {
            return $this->executeMethod($this->classes[$procedure][0], $this->classes[$procedure][1], $params);
        }

        foreach ($this->instances as $instance) {
            if (method_exists($instance, $procedure)) {
                return $this->executeMethod($instance, $procedure, $params);
            }
        }

        throw new BadFunctionCallException('Unable to find the procedure');
    }

    /**
     * Execute a callback
     *
     * @access public
     * @param  Closure   $callback     Callback
     * @param  array     $params       Procedure params
     * @return mixed
     */
    public function executeCallback(Closure $callback, $params)
    {
        $reflection = new ReflectionFunction($callback);

        $arguments = $this->getArguments(
            $params,
            $reflection->getParameters(),
            $reflection->getNumberOfRequiredParameters(),
            $reflection->getNumberOfParameters()
        );

        return $reflection->invokeArgs($arguments);
    }

    /**
     * Execute a method
     *
     * @access public
     * @param  mixed     $class        Class name or instance
     * @param  string    $method       Method name
     * @param  array     $params       Procedure params
     * @return mixed
     */
    public function executeMethod($class, $method, $params)
    {
        $instance = is_string($class) ? new $class : $class;

        // Execute before action
        if (! empty($this->before)) {
            if (is_callable($this->before)) {
                call_user_func_array($this->before, array($this->getUsername(), $this->getPassword(), get_class($class), $method));
            }
            else if (method_exists($instance, $this->before)) {
                $instance->{$this->before}($this->getUsername(), $this->getPassword(), get_class($class), $method);
            }
        }

        $reflection = new ReflectionMethod($class, $method);

        $arguments = $this->getArguments(
            $params,
            $reflection->getParameters(),
            $reflection->getNumberOfRequiredParameters(),
            $reflection->getNumberOfParameters()
        );

        return $reflection->invokeArgs($instance, $arguments);
    }

    /**
     * Get procedure arguments
     *
     * @access public
     * @param  array    $request_params       Incoming arguments
     * @param  array    $method_params        Procedure arguments
     * @param  integer  $nb_required_params   Number of required parameters
     * @param  integer  $nb_max_params        Maximum number of parameters
     * @return array
     */
    public function getArguments(array $request_params, array $method_params, $nb_required_params, $nb_max_params)
    {
        $nb_params = count($request_params);

        if ($nb_params < $nb_required_params) {
            throw new InvalidArgumentException('Wrong number of arguments');
        }

        if ($nb_params > $nb_max_params) {
            throw new InvalidArgumentException('Too many arguments');
        }

        if ($this->isPositionalArguments($request_params, $method_params)) {
            return $request_params;
        }

        return $this->getNamedArguments($request_params, $method_params);
    }

    /**
     * Return true if we have positional parametes
     *
     * @access public
     * @param  array    $request_params      Incoming arguments
     * @param  array    $method_params       Procedure arguments
     * @return bool
     */
    public function isPositionalArguments(array $request_params, array $method_params)
    {
        return array_keys($request_params) === range(0, count($request_params) - 1);
    }

    /**
     * Get named arguments
     *
     * @access public
     * @param  array    $request_params      Incoming arguments
     * @param  array    $method_params       Procedure arguments
     * @return array
     */
    public function getNamedArguments(array $request_params, array $method_params)
    {
        $params = array();

        foreach ($method_params as $p) {

            $name = $p->getName();

            if (isset($request_params[$name])) {
                $params[$name] = $request_params[$name];
            }
            else if ($p->isDefaultValueAvailable()) {
                $params[$name] = $p->getDefaultValue();
            }
            else {
                throw new InvalidArgumentException('Missing argument: '.$name);
            }
        }

        return $params;
    }
}
