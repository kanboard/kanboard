<?php

use JsonRPC\Exception\AuthenticationFailureException;
use JsonRPC\MiddlewareHandler;
use JsonRPC\MiddlewareInterface;

require_once __DIR__.'/../../../vendor/autoload.php';

class FirstMiddleware implements MiddlewareInterface
{
    public function execute($username, $password, $procedureName)
    {
    }
}

class SecondMiddleware implements MiddlewareInterface
{
    public function execute($username, $password, $procedureName)
    {
        if ($username === 'myUsername' && $password === 'myPassword' && $procedureName === 'myProcedure') {
            throw new AuthenticationFailureException('Bad user');
        }
    }
}

class MiddlewareHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testMiddlewareCanRaiseException()
    {
        $this->expectException('JsonRpc\Exception\AuthenticationFailureException');

        $middlewareHandler = new MiddlewareHandler();
        $middlewareHandler->withUsername('myUsername');
        $middlewareHandler->withPassword('myPassword');
        $middlewareHandler->withProcedure('myProcedure');
        $middlewareHandler->withMiddleware(new FirstMiddleware());
        $middlewareHandler->withMiddleware(new SecondMiddleware());
        $middlewareHandler->execute();
    }
}
