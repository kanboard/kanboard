<?php

use JsonRPC\Exception\AccessDeniedException;
use JsonRPC\Exception\AuthenticationFailureException;
use JsonRPC\Exception\InvalidJsonFormatException;
use JsonRPC\Exception\InvalidJsonRpcFormatException;
use JsonRPC\Exception\ResponseEncodingFailureException;
use JsonRPC\Exception\ResponseException;
use JsonRPC\Response\ResponseBuilder;

require_once __DIR__.'/../../../../vendor/autoload.php';

class ResponseBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testBuildResponse()
    {
        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","result":"test","id":123}', $response);
    }

    public function testBuildResponseWithError()
    {
        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->withError(42, 'Test', 'More info')
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":42,"message":"Test","data":"More info"},"id":123}', $response);
    }

    public function testBuildResponseWithException()
    {
        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->withException(new Exception('Test'))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":0,"message":"Test"},"id":123}', $response);
    }

    public function testBuildResponseWithResponseException()
    {
        $exception = new ResponseException('Error', 42);
        $exception->setData('Data');

        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->withException($exception)
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":42,"message":"Error","data":"Data"},"id":123}', $response);
    }

    public function testBuildResponseWithAccessDeniedException()
    {
        $responseBuilder = ResponseBuilder::create();
        $response = $responseBuilder
            ->withId(123)
            ->withResult('test')
            ->withException(new AccessDeniedException('Test'))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":403,"message":"Forbidden"},"id":123}', $response);
        $this->assertEquals('HTTP/1.0 403 Forbidden', $responseBuilder->getStatus());

        $this->assertEquals(
            array('Content-Type' => 'application/json'),
            $responseBuilder->getHeaders()
        );
    }

    public function testBuildResponseWithAuthenticationFailureException()
    {
        $responseBuilder = ResponseBuilder::create();
        $response = $responseBuilder
            ->withId(123)
            ->withResult('test')
            ->withException(new AuthenticationFailureException('Test'))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":401,"message":"Unauthorized"},"id":123}', $response);
        $this->assertEquals('HTTP/1.0 401 Unauthorized', $responseBuilder->getStatus());

        $this->assertEquals(
            array('Content-Type' => 'application/json', 'WWW-Authenticate' => 'Basic realm="JsonRPC"'),
            $responseBuilder->getHeaders()
        );
    }

    public function testBuildResponseWithResponseEncodingFailureException()
    {
        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->withException(new ResponseEncodingFailureException('Test'))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":-32603,"message":"Internal error","data":"Test"},"id":123}', $response);
    }

    public function testBuildResponseWithInvalidArgumentException()
    {
        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->withException(new InvalidArgumentException('Test'))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":-32602,"message":"Invalid params","data":"Test"},"id":123}', $response);
    }

    public function testBuildResponseWithBadFunctionCallException()
    {
        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->withException(new BadFunctionCallException('Test'))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":123}', $response);
    }

    public function testBuildResponseWithInvalidJsonRpcFormatException()
    {
        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->withException(new InvalidJsonRpcFormatException('Test'))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null}', $response);
    }

    public function testBuildResponseWithInvalidJsonFormatException()
    {
        $response = ResponseBuilder::create()
            ->withId(123)
            ->withResult('test')
            ->withException(new InvalidJsonFormatException('Test'))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","error":{"code":-32700,"message":"Parse error"},"id":null}', $response);
    }
}
