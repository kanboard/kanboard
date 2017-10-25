<?php

namespace JsonRPC\Validator;

use JsonRPC\Exception\InvalidJsonFormatException;

/**
 * Class JsonFormatValidator
 *
 * @package JsonRPC\Validator
 * @author  Frederic Guillot
 */
class JsonFormatValidator
{
    /**
     * Validate
     *
     * @static
     * @access public
     * @param  mixed $payload
     * @throws InvalidJsonFormatException
     */
    public static function validate($payload)
    {
        if (! is_array($payload)) {
            throw new InvalidJsonFormatException('Malformed payload');
        }
    }
}

