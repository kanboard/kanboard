<?php

namespace JsonRPC\Validator;

use JsonRPC\Exception\InvalidJsonRpcFormatException;

/**
 * Class RpcFormatValidator
 *
 * @package JsonRPC\Validator
 * @author  Frederic Guillot
 */
class RpcFormatValidator
{
    /**
     * Validate
     *
     * @static
     * @access public
     * @param  array $payload
     * @throws InvalidJsonRpcFormatException
     */
    public static function validate(array $payload)
    {
        if (! isset($payload['jsonrpc']) ||
            ! isset($payload['method']) ||
            ! is_string($payload['method']) ||
            $payload['jsonrpc'] !== '2.0' ||
            (isset($payload['params']) && ! is_array($payload['params']))) {

            throw new InvalidJsonRpcFormatException('Invalid JSON RPC payload');
        }
    }
}

