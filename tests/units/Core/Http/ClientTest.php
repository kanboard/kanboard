<?php

namespace KanboardTests\units\Core\Http;

use KanboardTests\units\Base;
use Kanboard\Core\Http\Client;

class ClientTest extends Base
{
    public function testGetCanDisableRedirects()
    {
        $client = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('doRequest'))
            ->getMock();

        $client
            ->expects($this->once())
            ->method('doRequest')
            ->with('GET', 'https://example.org', '', array('X-Test: 1'), true, false)
            ->willReturn('response-body');

        $this->assertSame(
            'response-body',
            $client->get('https://example.org', array('X-Test: 1'), true, false)
        );
    }

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

    public function testIsPrivateUrlWithNumericHostForms()
    {
        $client = new Client($this->container);

        // Numeric forms accepted by curl/libcurl: inet_pton() alone rejects them,
        // so they are parsed explicitly instead of relying on DNS resolution.
        $this->assertTrue($client->isPrivateURL('http://2130706433/'));
        $this->assertTrue($client->isPrivateURL('http://127.1/'));
        $this->assertTrue($client->isPrivateURL('http://017700000001/'));
        $this->assertTrue($client->isPrivateURL('http://0x7f000001/'));
        $this->assertTrue($client->isPrivateURL('http://0x7f000001:9600/leak'));
        $this->assertTrue($client->isPrivateURL('http://0x7f.0.0.1/'));
        $this->assertTrue($client->isPrivateURL('http://127.000.000.001/'));
        $this->assertTrue($client->isPrivateURL('http://[::1]/'));
    }

    public function testIsPrivateUrlWithPublicIpv6Literal()
    {
        $client = new Client($this->container);

        // Public IPv6 literals must not be blocked (and must not need a DNS lookup)
        $start = microtime(true);
        $this->assertFalse($client->isPrivateURL('http://[2001:4860:4860::8888]/'));
        $this->assertFalse($client->isPrivateURL('http://2606:4700:4700::1111/'));
        $this->assertLessThan(1.0, microtime(true) - $start);
    }

    public function testIsPrivateUrlWithMetadataAndReservedRanges()
    {
        $client = new Client($this->container);

        $this->assertTrue($client->isPrivateURL('http://169.254.169.254/latest/meta-data/'));
        $this->assertTrue($client->isPrivateURL('http://100.64.0.1/'));
        $this->assertTrue($client->isPrivateURL('http://192.168.0.1:8080/'));
    }

    public function testIsPrivateUrlDoesNotBlockUnresolvableHost()
    {
        $client = new Client($this->container);

        // Proxy only setups have no DNS: an unresolvable host is not proof of a
        // private target, the connection attempt decides.
        $this->assertFalse($client->isPrivateURL('http://host.invalid/'));
    }

    public function testCheckUrlPinsResolvedAddress()
    {
        $client = new Client($this->container);

        $this->assertFalse($client->checkUrl('http://0x7f000001/'));
        $this->assertFalse($client->checkUrl('http://169.254.169.254/latest/meta-data/'));

        // A public URL is returned unchanged and its address is reused for the request
        $this->assertSame('http://example.com/', $client->checkUrl('http://example.com/'));

        $method = new \ReflectionMethod(Client::class, 'getResolveOptions');
        $method->setAccessible(true);
        $options = $method->invoke($client, 'http://example.com/');

        $this->assertNotEmpty($options);
        $this->assertStringStartsWith('example.com:80:', $options[0][0]);
    }
}
