<?php

namespace Kanboard\Core\Http;

class InvalidStatusException extends ClientException
{
    protected $statusCode = 0;

    public function __construct($message, $statusCode)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
