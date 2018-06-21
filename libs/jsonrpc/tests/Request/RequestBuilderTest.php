<?php

use JsonRPC\Request\RequestBuilder;

require_once __DIR__.'/../../../../vendor/autoload.php';

class RequestBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testBuilder()
    {
        $payload = RequestBuilder::create()
            ->withId(123)
            ->withProcedure('foobar')
            ->withParams(array(1, 2, 3))
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","method":"foobar","id":123,"params":[1,2,3]}', $payload);
    }

    public function testBuilderWithoutParams()
    {
        $payload = RequestBuilder::create()
            ->withId(123)
            ->withProcedure('foobar')
            ->build();

        $this->assertEquals('{"jsonrpc":"2.0","method":"foobar","id":123}', $payload);
    }

    public function testBuilderWithoutId()
    {
        $payload = RequestBuilder::create()
            ->withProcedure('foobar')
            ->withParams(array(1, 2, 3))
            ->build();

        $result = json_decode($payload, true);
        $this->assertNotNull($result['id']);
    }

    public function testBuilderWithAdditionalRequestAttributes()
    {
        $payload = RequestBuilder::create()
            ->withProcedure('foobar')
            ->withParams(array(1, 2, 3))
            ->withRequestAttributes(array("some-attr" => 42))
            ->build();

        $result = json_decode($payload, true);
        $this->assertNotNull($result['some-attr']);
    }

}
