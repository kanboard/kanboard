<?php

use JsonRPC\Client;

require_once __DIR__.'/../../../vendor/autoload.php';

class ClientTest extends PHPUnit_Framework_TestCase
{
    private $httpClient;

    public function setUp()
    {
        $this->httpClient = $this
            ->getMockBuilder('\JsonRPC\HttpClient')
            ->setMethods(array('execute'))
            ->getMock();
    }

    public function testSendBatch()
    {
        $client = new Client('', false, $this->httpClient);
        $response = array(
            array(
                'jsonrpc' => '2.0',
                'result' => 'c',
                'id' => 1,
            ),
            array(
                'jsonrpc' => '2.0',
                'result' => 'd',
                'id' => 2,
            )
        );

        $this->httpClient
            ->expects($this->once())
            ->method('execute')
            ->with($this->stringContains('[{"jsonrpc":"2.0","method":"methodA","id":'))
            ->will($this->returnValue($response));


        $result = $client->batch()
            ->execute('methodA', array('a' => 'b'))
            ->execute('methodB', array('a' => 'b'))
            ->send();

        $this->assertEquals(array('c', 'd'), $result);
    }

    public function testSendRequest()
    {
        $client = new Client('', false, $this->httpClient);

        $this->httpClient
            ->expects($this->once())
            ->method('execute')
            ->with($this->stringContains('{"jsonrpc":"2.0","method":"methodA","id":'))
            ->will($this->returnValue(array('jsonrpc' => '2.0', 'result' => 'foobar', 'id' => 1)));

        $result = $client->execute('methodA', array('a' => 'b'));
        $this->assertEquals($result, 'foobar');
    }

    public function testSendRequestWithError()
    {
        $client = new Client('', false, $this->httpClient);

        $this->httpClient
            ->expects($this->once())
            ->method('execute')
            ->with($this->stringContains('{"jsonrpc":"2.0","method":"methodA","id":'))
            ->will($this->returnValue(array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => -32601,
                    'message' => 'Method not found',
                ),
            )));

        $this->expectException('BadFunctionCallException');
        $client->execute('methodA', array('a' => 'b'));
    }

    public function testSendRequestWithErrorAndReturnExceptionEnabled()
    {
        $client = new Client('', true, $this->httpClient);

        $this->httpClient
            ->expects($this->once())
            ->method('execute')
            ->with($this->stringContains('{"jsonrpc":"2.0","method":"methodA","id":'))
            ->will($this->returnValue(array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => -32601,
                    'message' => 'Method not found',
                ),
            )));

        $result = $client->execute('methodA', array('a' => 'b'));
        $this->assertInstanceOf('BadFunctionCallException', $result);
    }
}
