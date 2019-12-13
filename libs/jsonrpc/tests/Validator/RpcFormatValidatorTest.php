<?php

use JsonRPC\Validator\RpcFormatValidator;

require_once __DIR__.'/../../../../vendor/autoload.php';

class RpcFormatValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testWithMinimumRequirement()
    {
        $this->assertNull(RpcFormatValidator::validate(array('jsonrpc' => '2.0', 'method' => 'foobar')));
    }

    public function testWithNoVersion()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonRpcFormatException');
        RpcFormatValidator::validate(array('method' => 'foobar'));
    }

    public function testWithNoMethod()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonRpcFormatException');
        RpcFormatValidator::validate(array('jsonrpc' => '2.0'));
    }

    public function testWithMethodNotString()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonRpcFormatException');
        RpcFormatValidator::validate(array('jsonrpc' => '2.0', 'method' => array()));
    }

    public function testWithBadVersion()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonRpcFormatException');
        RpcFormatValidator::validate(array('jsonrpc' => '1.0', 'method' => 'abc'));
    }

    public function testWithBadParams()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonRpcFormatException');
        RpcFormatValidator::validate(array('jsonrpc' => '2.0', 'method' => 'abc', 'params' => 'foobar'));
    }

    public function testWithParams()
    {
        $this->assertNull(RpcFormatValidator::validate(array('jsonrpc' => '2.0', 'method' => 'abc', 'params' => array(1, 2))));
    }
}
