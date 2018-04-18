<?php

namespace Kanboard\Core\Http;

class InvalidStatusException extends ClientException
{
    protected $statusCode = 0;
    protected $body = '';

    public function __construct($message, $statusCode, $body)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }
}
