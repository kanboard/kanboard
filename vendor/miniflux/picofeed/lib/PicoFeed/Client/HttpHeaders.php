<?php

namespace PicoFeed\Client;

use ArrayAccess;
use PicoFeed\Logging\Logger;

/**
 * Class to handle HTTP headers case insensitivity.
 *
 * @author  Bernhard Posselt
 * @author  Frederic Guillot
 */
class HttpHeaders implements ArrayAccess
{
    private $headers = array();

    public function __construct(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->headers[strtolower($key)] = $value;
        }
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->headers[strtolower($offset)] : '';
    }

    public function offsetSet($offset, $value)
    {
        $this->headers[strtolower($offset)] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->headers[strtolower($offset)]);
    }

    public function offsetUnset($offset)
    {
        unset($this->headers[strtolower($offset)]);
    }

    /**
     * Parse HTTP headers.
     *
     * @static
     *
     * @param array $lines List of headers
     *
     * @return array
     */
    public static function parse(array $lines)
    {
        $status = 0;
        $headers = array();

        foreach ($lines as $line) {
            if (strpos($line, 'HTTP/1') === 0) {
                $headers = array();
                $status = (int) substr($line, 9, 3);
            } elseif (strpos($line, ': ') !== false) {
                list($name, $value) = explode(': ', $line);
                if ($value) {
                    $headers[trim($name)] = trim($value);
                }
            }
        }

        Logger::setMessage(get_called_class().' HTTP status code: '.$status);

        foreach ($headers as $name => $value) {
            Logger::setMessage(get_called_class().' HTTP header: '.$name.' => '.$value);
        }

        return array($status, new self($headers));
    }
}
