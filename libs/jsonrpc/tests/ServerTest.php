<?php

use JsonRPC\Exception\AccessDeniedException;
use JsonRPC\Exception\AuthenticationFailureException;
use JsonRPC\Exception\ResponseException;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Response\HeaderMockTest;
use JsonRPC\Server;

require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/Response/HeaderMockTest.php';

class MyException extends Exception
{

}

class DummyMiddleware implements MiddlewareInterface
{
    public function execute($username, $password, $procedureName)
    {
        throw new AuthenticationFailureException('Bad user');
    }
}

class ServerTest extends HeaderMockTest
{
    private $payload = '{"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"}';

    public function testCustomAuthenticationHeader()
    {
        $env = array(
            'HTTP_X_AUTH' => base64_encode('myuser:mypassword'),
        );

        $server = new Server($this->payload, $env);
        $server->setAuthenticationHeader('X-Auth');
        $this->assertEquals('myuser', $server->getUsername());
        $this->assertEquals('mypassword', $server->getPassword());
    }

    public function testCustomAuthenticationHeaderWithEmptyValue()
    {
        $server = new Server($this->payload);
        $server->setAuthenticationHeader('X-Auth');
        $this->assertNull($server->getUsername());
        $this->assertNull($server->getPassword());
    }

    public function testGetUsername()
    {
        $server = new Server($this->payload);
        $this->assertNull($server->getUsername());

        $server = new Server($this->payload, array('PHP_AUTH_USER' => 'username'));
        $this->assertEquals('username', $server->getUsername());
    }

    public function testGetPassword()
    {
        $server = new Server($this->payload);
        $this->assertNull($server->getPassword());

        $server = new Server($this->payload, array('PHP_AUTH_PW' => 'password'));
        $this->assertEquals('password', $server->getPassword());
    }

    public function testExecute()
    {
        $server = new Server($this->payload);
        $server->getProcedureHandler()->withCallback('sum', function($a, $b, $c) {
            return $a + $b + $c;
        });

        self::$functions
            ->expects($this->once())
            ->method('header')
            ->with('Content-Type: application/json');

        $this->assertEquals('{"jsonrpc":"2.0","result":7,"id":"1"}', $server->execute());
    }

    public function testExecuteRequestParserOverride()
    {
        $requestParser = $this->getMockBuilder('JsonRPC\Request\RequestParser')
            ->getMock();

        $requestParser->method('withPayload')->willReturn($requestParser);
        $requestParser->method('withProcedureHandler')->willReturn($requestParser);
        $requestParser->method('withMiddlewareHandler')->willReturn($requestParser);
        $requestParser->method('withLocalException')->willReturn($requestParser);

        $server = new Server($this->payload, array(), null, $requestParser);

        $requestParser->expects($this->once())
            ->method('parse');

        $server->execute();
    }

    public function testExecuteBatchRequestParserOverride()
    {
        $batchRequestParser = $this->getMockBuilder('JsonRPC\Request\BatchRequestParser')
            ->getMock();

        $batchRequestParser->method('withPayload')->willReturn($batchRequestParser);
        $batchRequestParser->method('withProcedureHandler')->willReturn($batchRequestParser);
        $batchRequestParser->method('withMiddlewareHandler')->willReturn($batchRequestParser);
        $batchRequestParser->method('withLocalException')->willReturn($batchRequestParser);

        $server = new Server('["...", "..."]', array(), null, null, $batchRequestParser);

        $batchRequestParser->expects($this->once())
            ->method('parse');

        $server->execute();
    }

    public function testExecuteResponseBuilderOverride()
    {
        $responseBuilder = $this->getMockBuilder('JsonRPC\Response\ResponseBuilder')
            ->getMock();

        $responseBuilder->expects($this->once())
            ->method('sendHeaders');

        $server = new Server($this->payload, array(), $responseBuilder);
        $server->execute();
    }

    public function testExecuteProcedureHandlerOverride()
    {
        $batchRequestParser = $this->getMockBuilder('JsonRPC\Request\BatchRequestParser')
            ->getMock();

        $procedureHandler = $this->getMockBuilder('JsonRPC\ProcedureHandler')
            ->getMock();

        $batchRequestParser->method('withPayload')->willReturn($batchRequestParser);
        $batchRequestParser->method('withProcedureHandler')->willReturn($batchRequestParser);
        $batchRequestParser->method('withMiddlewareHandler')->willReturn($batchRequestParser);
        $batchRequestParser->method('withLocalException')->willReturn($batchRequestParser);

        $server = new Server('["...", "..."]', array(), null, null, $batchRequestParser, $procedureHandler);

        $batchRequestParser->expects($this->once())
            ->method('parse');

        $batchRequestParser->expects($this->once())
            ->method('withProcedureHandler')
            ->with($this->identicalTo($procedureHandler));

        $server->execute();
    }

    public function testWhenCallbackRaiseForbiddenException()
    {
        $server = new Server($this->payload);
        $server->getProcedureHandler()->withCallback('sum', function($a, $b, $c) {
            throw new AccessDeniedException();
        });

        self::$functions
            ->expects($this->at(0))
            ->method('header')
            ->with('HTTP/1.0 403 Forbidden');

        self::$functions
            ->expects($this->at(1))
            ->method('header')
            ->with('Content-Type: application/json');

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":403,"message":"Forbidden"},"id":null}', $server->execute());
    }

    public function testWhenCallbackRaiseUnauthorizedException()
    {
        $server = new Server($this->payload);
        $server->getProcedureHandler()->withCallback('sum', function($a, $b, $c) {
            throw new AuthenticationFailureException();
        });

        self::$functions
            ->expects($this->at(0))
            ->method('header')
            ->with('HTTP/1.0 401 Unauthorized');

        self::$functions
            ->expects($this->at(1))
            ->method('header')
            ->with('Content-Type: application/json');

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":401,"message":"Unauthorized"},"id":null}', $server->execute());
    }

    public function testWhenMiddlewareRaiseUnauthorizedException()
    {
        $server = new Server($this->payload);
        $server->getMiddlewareHandler()->withMiddleware(new DummyMiddleware());
        $server->getProcedureHandler()->withCallback('sum', function($a, $b) {
            return $a + $b;
        });

        self::$functions
            ->expects($this->at(0))
            ->method('header')
            ->with('HTTP/1.0 401 Unauthorized');

        self::$functions
            ->expects($this->at(1))
            ->method('header')
            ->with('Content-Type: application/json');

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":401,"message":"Unauthorized"},"id":null}', $server->execute());
    }

    public function testFilterRelayExceptions()
    {
        $server = new Server($this->payload);
        $server->withLocalException('MyException');
        $server->getProcedureHandler()->withCallback('sum', function($a, $b, $c) {
            throw new MyException('test');
        });

        $this->expectException('MyException');
        $server->execute();
    }

    public function testCustomExceptionAreRelayedToClient()
    {
        $server = new Server($this->payload);
        $server->getProcedureHandler()->withCallback('sum', function($a, $b, $c) {
            throw new MyException('test');
        });

        self::$functions
            ->expects($this->once())
            ->method('header')
            ->with('Content-Type: application/json');

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":0,"message":"test"},"id":"1"}', $server->execute());
    }

    public function testCustomResponseException()
    {
        $server = new Server($this->payload);
        $server->getProcedureHandler()->withCallback('sum', function($a, $b, $c) {
            throw new ResponseException('test', 123, null, 'more info');
        });

        self::$functions
            ->expects($this->once())
            ->method('header')
            ->with('Content-Type: application/json');

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":123,"message":"test","data":"more info"},"id":"1"}', $server->execute());
    }
}
