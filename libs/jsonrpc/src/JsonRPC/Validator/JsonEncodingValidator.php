<?php

namespace JsonRPC\Validator;

use JsonRPC\Exception\ResponseEncodingFailureException;

/**
 * Class JsonEncodingValidator
 *
 * @package JsonRPC\Validator
 * @author  Frederic Guillot
 */
class JsonEncodingValidator
{
    public static function validate()
    {
        $jsonError = json_last_error();

        if ($jsonError !== JSON_ERROR_NONE) {
            switch ($jsonError) {
                case JSON_ERROR_DEPTH:
                    $errorMessage = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $errorMessage = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $errorMessage = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $errorMessage = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $errorMessage = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $errorMessage = 'Unknown error';
                    break;
            }

            throw new ResponseEncodingFailureException($errorMessage, $jsonError);
        }
    }
}
