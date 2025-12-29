<?php

namespace KanboardTests\units\Core\Http;

use KanboardTests\units\Base;
use Kanboard\Core\Http\Client;

class ClientTest extends Base
{
    public function testIsPrivateIpAddressWithPrivateIPv4()
    {
        $client = new Client($this->container);

        $this->assertTrue($client->isPrivateIpAddress('10.0.0.1'));
        $this->assertTrue($client->isPrivateIpAddress('172.16.5.10'));
        $this->assertTrue($client->isPrivateIpAddress('192.168.1.20'));
        $this->assertTrue($client->isPrivateIpAddress('127.0.0.1'));
    }

    public function testIsPrivateIpAddressWithPrivateIPv6()
    {
        $client = new Client($this->container);

        $this->assertTrue($client->isPrivateIpAddress('::1'));
        $this->assertTrue($client->isPrivateIpAddress('fd12:3456:789a:1::1'));
    }

    public function testIsPrivateIpAddressWithPublicAndInvalidValues()
    {
        $client = new Client($this->container);

        $this->assertFalse($client->isPrivateIpAddress('8.8.8.8'));
        $this->assertFalse($client->isPrivateIpAddress('2607:f8b0:4005:805::200e'));
        $this->assertFalse($client->isPrivateIpAddress('not-an-ip'));
        $this->assertFalse($client->isPrivateIpAddress(''));
    }

    public function testIsPrivateUrlWithPrivateHostnames()
    {
        $client = new Client($this->container);

        $this->assertTrue($client->isPrivateURL('http://localhost'));
        $this->assertTrue($client->isPrivateURL('http://127.0.0.1/path'));
        $this->assertTrue($client->isPrivateURL('http://10.0.0.15/api'));
    }

    public function testIsPrivateUrlWithPublicAddresses()
    {
        $client = new Client($this->container);

        $this->assertFalse($client->isPrivateURL('http://8.8.8.8/data'));
        $this->assertFalse($client->isPrivateURL('https://ipv6.google.com/test'));
    }

    public function testIsPrivateUrlWithUnsupportedScheme()
    {
        $client = new Client($this->container);

        $this->assertFalse($client->isPrivateURL('ftp://127.0.0.1'));
        $this->assertFalse($client->isPrivateURL('mailto:test@example.com'));
    }
}
