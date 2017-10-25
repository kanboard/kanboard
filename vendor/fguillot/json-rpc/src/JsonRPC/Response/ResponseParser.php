<?php

namespace JsonRPC\Response;

use BadFunctionCallException;
use InvalidArgumentException;
use Exception;
use JsonRPC\Exception\InvalidJsonFormatException;
use JsonRPC\Exception\InvalidJsonRpcFormatException;
use JsonRPC\Exception\ResponseException;
use JsonRPC\Validator\JsonFormatValidator;

/**
 * Class ResponseParser
 *
 * @package JsonRPC\Request
 * @author  Frederic Guillot
 */
class ResponseParser
{
    /**
     * Payload
     *
     * @access private
     * @var mixed
     */
    private $payload;

    /**
     * Do not immediately throw an exception on error. Return it instead.
     *
     * @var bool
     */
    private $returnException = false;

    /**
     * Get new object instance
     *
     * @static
     * @access public
     * @return ResponseParser
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set Return Exception Or Throw It
     *
     * @param $returnException
     * @return ResponseParser
     */
    public function withReturnException($returnException)
    {
        $this->returnException = $returnException;
        return $this;
    }

    /**
     * Set payload
     *
     * @access public
     * @param  mixed $payload
     * @return $this
     */
    public function withPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Parse response
     *
     * @return array|Exception|null
     * @throws InvalidJsonFormatException
     * @throws BadFunctionCallException
     * @throws InvalidJsonRpcFormatException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ResponseException
     */
    public function parse()
    {
        JsonFormatValidator::validate($this->payload);

        if ($this->isBatchResponse()) {
            $results = array();

            foreach ($this->payload as $response) {
                $results[] = self::create()
                    ->withReturnException($this->returnException)
                    ->withPayload($response)
                    ->parse();
            }

            return $results;
        }

        if (isset($this->payload['error']['code'])) {
            try {
                $this->handleExceptions();
            } catch (Exception $e) {
                if ($this->returnException) {
                    return $e;
                }
                throw $e;
            }
        }

        return isset($this->payload['result']) ? $this->payload['result'] : null;
    }

    /**
     * Handle exceptions
     *
     * @access private
     * @throws InvalidJsonFormatException
     * @throws InvalidJsonRpcFormatException
     * @throws ResponseException
     */
    private function handleExceptions()
    {
        switch ($this->payload['error']['code']) {
            case -32700:
                throw new InvalidJsonFormatException('Parse error: '.$this->payload['error']['message']);
            case -32600:
                throw new InvalidJsonRpcFormatException('Invalid Request: '.$this->payload['error']['message']);
            case -32601:
                throw new BadFunctionCallException('Procedure not found: '.$this->payload['error']['message']);
            case -32602:
                throw new InvalidArgumentException('Invalid arguments: '.$this->payload['error']['message']);
            default:
                throw new ResponseException(
                    $this->payload['error']['message'],
                    $this->payload['error']['code'],
                    null,
                    isset($this->payload['error']['data']) ? $this->payload['error']['data'] : null
                );
        }
    }

    /**
     * Return true if we have a batch response
     *
     * @access private
     * @return boolean
     */
    private function isBatchResponse()
    {
        return array_keys($this->payload) === range(0, count($this->payload) - 1);
    }
}
