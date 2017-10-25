<?php

namespace JsonRPC\Exception;

use Exception;

/**
 * Class ResponseException
 *
 * @package JsonRPC\Exception
 * @author  Frederic Guillot
 */
class ResponseException extends Exception
{
    /**
     * A value that contains additional information about the error.
     *
     * @access protected
     * @link http://www.jsonrpc.org/specification#error_object
     * @var mixed
     */
    protected $data;

    /**
     * Constructor
     *
     * @access public
     * @param string    $message  [optional] The Exception message to throw.
     * @param int       $code     [optional] The Exception code.
     * @param Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
     * @param mixed     $data     [optional] A value that contains additional information about the error.
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, $data = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setData($data);
    }

    /**
     * Attach additional information
     *
     * @access public
     * @param mixed $data [optional] A value that contains additional information about the error.
     * @return \JsonRPC\Exception\ResponseException
     */
    public function setData($data = null)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get additional information
     *
     * @access public
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }
}
