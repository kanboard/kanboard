<?php

use JsonRPC\Response\ResponseParser;

require_once __DIR__.'/../../../../vendor/autoload.php';

class ResponseParserTest extends PHPUnit_Framework_TestCase
{
    public function testSingleRequest()
    {
        $result = ResponseParser::create()
            ->withPayload(json_decode('{"jsonrpc": "2.0", "result": "foobar", "id": "1"}', true))
            ->parse();

        $this->assertEquals('foobar', $result);
    }

    public function testWithBadJsonFormat()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonFormatException');

        ResponseParser::create()
            ->withPayload('foobar')
            ->parse();
    }

    public function testWithBadProcedure()
    {
        $this->expectException('BadFunctionCallException');

        ResponseParser::create()
            ->withPayload(json_decode('{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": "1"}', true))
            ->parse();
    }

    public function testWithInvalidArgs()
    {
        $this->expectException('InvalidArgumentException');

        ResponseParser::create()
            ->withPayload(json_decode('{"jsonrpc": "2.0", "error": {"code": -32602, "message": "Invalid params"}, "id": "1"}', true))
            ->parse();
    }

    public function testWithInvalidRequest()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonRpcFormatException');

        ResponseParser::create()
            ->withPayload(json_decode('{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}', true))
            ->parse();
    }

    public function testWithParseError()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonFormatException');

        ResponseParser::create()
            ->withPayload(json_decode('{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}', true))
            ->parse();
    }

    public function testWithOtherError()
    {
        $this->expectException('\JsonRPC\Exception\ResponseException');

        ResponseParser::create()
            ->withPayload(json_decode('{"jsonrpc": "2.0", "error": {"code": 42, "message": "Something", "data": "foobar"}, "id": null}', true))
            ->parse();
    }

    public function testBatch()
    {
        $payload = '[
            {"jsonrpc": "2.0", "result": 7, "id": "1"},
            {"jsonrpc": "2.0", "result": 19, "id": "2"}
        ]';

        $result = ResponseParser::create()
            ->withPayload(json_decode($payload, true))
            ->parse();

        $this->assertEquals(array(7, 19), $result);
    }

    public function testBatchWithError()
    {
        $payload = '[
            {"jsonrpc": "2.0", "result": 7, "id": "1"},
            {"jsonrpc": "2.0", "result": 19, "id": "2"},
            {"jsonrpc": "2.0", "error": {"code": -32602, "message": "Invalid params"}, "id": "1"}
        ]';

        $this->expectException('InvalidArgumentException');

        ResponseParser::create()
            ->withPayload(json_decode($payload, true))
            ->parse();
    }
}
