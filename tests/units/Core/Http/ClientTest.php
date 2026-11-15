<?php

namespace KanboardTests\units\Core\Http;

use KanboardTests\units\Base;
use Kanboard\Core\Http\Client;
use Kanboard\Core\Http\ClientException;
use Kanboard\Job\HttpAsyncJob;
use Psr\Log\NullLogger;
use ReflectionMethod;

class ClientTest extends Base
{
    private function getClientWithResolver(array $ipAddresses, $hostname = null)
    {
        $client = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('resolveHostname'))
            ->getMock();

        $method = $client->expects($this->once())->method('resolveHostname');

        if ($hostname !== null) {
            $method->with($hostname);
        }

        $method->willReturn($ipAddresses);

        return $client;
    }

    private function invokePrivateMethod(Client $client, $name, array $arguments)
    {
        return (new ReflectionMethod($client, $name))->invokeArgs($client, $arguments);
    }

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

    public function testIsPublicIpAddressWithNonPublicAddresses()
    {
        $client = new Client($this->container);

        $addresses = array(
            '0.0.0.0', '10.1.2.3', '100.64.0.1', '100.127.255.254', '127.0.0.1', '127.255.255.255',
            '169.254.169.254', '172.16.0.1', '172.31.255.255', '192.0.0.8', '192.0.2.10', '192.168.1.1',
            '198.18.0.1', '198.51.100.7', '203.0.113.9', '224.0.0.1', '255.255.255.255',
            '0.0.0.1', '0.1.2.3', '0.255.255.255', '240.0.0.0', '240.0.0.1', '247.1.2.3', '254.255.255.254',
            '::ffff:0.1.2.3', '::ffff:f000:1', '::0.1.2.3', '::240.0.0.1', '64:ff9b::0.1.2.3', '64:ff9b::f000:1',
            '2002:1:203::1', '2002:f000:1::1',
            '::', '::1', 'fc00::1', 'fd00::1', 'fe80::1', 'fec0::1', 'ff02::1', '100::1',
            '2001::1', '2001:2::1', '2001:db8::1', '3fff::1', '5f00::1',
            '::ffff:127.0.0.1', '::ffff:7f00:1', '::ffff:169.254.169.254', '::ffff:a9fe:a9fe',
            '::ffff:c0a8:0101', '0:0:0:0:0:ffff:7f00:1', '::127.0.0.1', '::a9fe:a9fe',
            '64:ff9b::127.0.0.1', '64:ff9b::a9fe:a9fe', '64:ff9b:1::a9fe:a9fe',
            '2002:7f00:1::', '2002:a9fe:a9fe::1', '2002:c0a8:101::',
            'not-an-ip', '', '127.1', '0x7f000001', '2130706433', 'fe80::1%eth0',
        );

        foreach ($addresses as $address) {
            $this->assertFalse($client->isPublicIpAddress($address), $address);
        }
    }

    public function testIsPublicIpAddressWithPublicAddresses()
    {
        $client = new Client($this->container);

        $addresses = array(
            '8.8.8.8', '1.1.1.1', '1.0.0.1', '93.184.215.14', '100.63.255.255', '100.128.0.1', '172.32.0.1',
            '192.169.0.1', '198.20.0.1', '223.255.255.255',
            '2607:f8b0:4005:805::200e', '2606:4700::1111', '2a00:1450:4001::1',
            '::ffff:8.8.8.8', '64:ff9b::8.8.8.8', '2002:808:808::1',
        );

        foreach ($addresses as $address) {
            $this->assertTrue($client->isPublicIpAddress($address), $address);
        }
    }

    public function testResolvePublicIpAddressRejectsNonPublicHosts()
    {
        $client = new Client($this->container);

        $urls = array(
            'http://127.0.0.1/', 'http://127.0.0.1.:9000/', 'http://LOCALHOST/', 'http://localhost./',
            'http://2130706433/', 'http://0x7f000001:9000/', 'http://0177.0.0.1/', 'http://127.1/', 'http://0/',
            'http://2852039166/', 'http://169.254.169.254/latest/meta-data/',
            'http://[::1]/', 'http://[::ffff:127.0.0.1]:9000/', 'http://[0:0:0:0:0:ffff:127.0.0.1]/',
            'http://[::ffff:169.254.169.254]/latest/meta-data/', 'http://[::ffff:a9fe:a9fe]/',
            'http://[::ffff:c0a8:0101]/', 'http://[::ffff:10.0.0.1]/', 'http://[fd00::1]/', 'http://[fe80::1]/',
            'http://[64:ff9b::a9fe:a9fe]/', 'http://[2002:a9fe:a9fe::]/', 'http://100.64.0.1/',
            'http://0.1.2.3/', 'http://0.255.255.255:8080/', 'http://240.0.0.1/', 'http://255.255.255.254/',
            'http://[::ffff:0.1.2.3]/', 'http://[::ffff:f000:1]/', 'http://[64:ff9b::f000:1]/', 'http://[2002:f000:1::]/',
            'https://user:password@10.0.0.1:8443/path?query=1',
        );

        foreach ($urls as $url) {
            $this->assertNull($client->resolvePublicIpAddress($url), $url);
        }
    }

    public function testResolvePublicIpAddressRejectsAlternativeNotationsWithoutResolving()
    {
        $client = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('resolveHostname'))
            ->getMock();

        $client
            ->expects($this->never())
            ->method('resolveHostname');

        $urls = array(
            'http://2130706433/', 'http://134744072/', 'http://0x7f000001/', 'http://0X08080808/', 'http://0177.0.0.1/',
            'http://127.1/', 'http://8.8.8.010/', 'http://0/', 'http://0x/', 'http://example.0x1/', 'http://example.123./',
            'http://[fe80::1%25eth0]/', 'http://[::ffff:127.0.0.1:9000/',
        );

        foreach ($urls as $url) {
            $this->assertNull($client->resolvePublicIpAddress($url), $url);
        }
    }

    public function testResolvePublicIpAddressRejectsUnsupportedUrls()
    {
        $client = new Client($this->container);

        $urls = array('', 'not a url', 'http:///path', 'ftp://8.8.8.8/', 'file:///etc/passwd', 'gopher://8.8.8.8/', 'mailto:test@example.com');

        foreach ($urls as $url) {
            $this->assertNull($client->resolvePublicIpAddress($url), $url);
        }
    }

    public function testResolvePublicIpAddressWithPublicIpLiterals()
    {
        $client = new Client($this->container);

        $this->assertSame('8.8.8.8', $client->resolvePublicIpAddress('http://8.8.8.8/data'));
        $this->assertSame('2606:4700::1111', $client->resolvePublicIpAddress('https://[2606:4700::1111]:8443/'));
    }

    public function testResolvePublicIpAddressWithHostname()
    {
        $client = $this->getClientWithResolver(array('93.184.215.14', '2606:2800:21f:cb07:6820:80da:af6b:8b2c'), 'example.org');

        $this->assertSame('93.184.215.14', $client->resolvePublicIpAddress('https://Example.ORG./path'));
    }

    public function testResolvePublicIpAddressRejectsHostnameWithAnyNonPublicAddress()
    {
        $this->assertNull($this->getClientWithResolver(array('93.184.215.14', '10.0.0.1'))->resolvePublicIpAddress('https://example.org/'));
        $this->assertNull($this->getClientWithResolver(array('2606:4700::1111', '::ffff:127.0.0.1'))->resolvePublicIpAddress('https://example.org/'));
    }

    public function testResolvePublicIpAddressRejectsUnresolvableHostname()
    {
        $this->assertNull($this->getClientWithResolver(array())->resolvePublicIpAddress('https://example.invalid/'));
    }

    public function testIsPrivateUrlWithPrivateHostnames()
    {
        $client = new Client($this->container);

        $this->assertTrue($client->isPrivateURL('http://localhost'));
        $this->assertTrue($client->isPrivateURL('http://127.0.0.1/path'));
        $this->assertTrue($client->isPrivateURL('http://10.0.0.15/api'));
        $this->assertTrue($client->isPrivateURL('http://[::ffff:127.0.0.1]:9000/'));
        $this->assertTrue($client->isPrivateURL('http://0x7f000001:9000/'));
        $this->assertTrue($client->isPrivateURL('http://127.0.0.1.:9000/'));
    }

    public function testIsPrivateUrlWithPublicAddresses()
    {
        $client = new Client($this->container);
        $this->assertFalse($client->isPrivateURL('http://8.8.8.8/data'));

        $client = $this->getClientWithResolver(array('142.250.69.206', '2607:f8b0:4005:805::200e'), 'ipv6.google.com');
        $this->assertFalse($client->isPrivateURL('https://ipv6.google.com/test'));
    }

    public function testIsPrivateUrlWithUnsupportedScheme()
    {
        $client = new Client($this->container);

        $this->assertFalse($client->isPrivateURL('ftp://127.0.0.1'));
        $this->assertFalse($client->isPrivateURL('mailto:test@example.com'));
    }

    public function testWithPrivateNetworksBlockedReturnsNewInstance()
    {
        $client = new Client($this->container);
        $blockedClient = $client->withPrivateNetworksBlocked();

        $this->assertInstanceOf(Client::class, $blockedClient);
        $this->assertNotSame($client, $blockedClient);
    }

    public function testBlockedClientRefusesNonPublicAddresses()
    {
        $this->container['logger'] = new NullLogger();
        $client = (new Client($this->container))->withPrivateNetworksBlocked();

        $this->assertSame('', $client->get('http://[::ffff:127.0.0.1]:9/'));
        $this->assertSame('', $client->postJson('http://0x7f000001:9/', array('key' => 'value')));

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Blocked URL: http://[::1]:9/');
        $client->get('http://[::1]:9/', array(), true);
    }

    public function testBlockedClientRefusesUnresolvableHostname()
    {
        $this->container['logger'] = new NullLogger();
        $client = $this->getClientWithResolver(array())->withPrivateNetworksBlocked();

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Blocked URL: https://example.invalid/');
        $client->get('https://example.invalid/', array(), true);
    }

    public function testBlockedClientKeepsFlagForAsyncRequests()
    {
        $this->container['queueManager'] = $this
            ->getMockBuilder('\Kanboard\Core\Queue\QueueManager')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('push'))
            ->getMock();

        $this->container['queueManager']
            ->expects($this->exactly(2))
            ->method('push')
            ->with($this->callback(function (HttpAsyncJob $job) {
                $params = $job->getJobParams();
                return $params[1] === 'https://example.org/' && $params[5] === true;
            }));

        $client = (new Client($this->container))->withPrivateNetworksBlocked();
        $client->postJsonAsync('https://example.org/', array('key' => 'value'));
        $client->postFormAsync('https://example.org/', array('key' => 'value'));
    }

    public function testCurlConnectToPinsIpAddress()
    {
        $client = new Client($this->container);

        $this->assertSame('::93.184.215.14:', $this->invokePrivateMethod($client, 'getCurlConnectTo', array('93.184.215.14')));
        $this->assertSame('::[2606:4700::1111]:', $this->invokePrivateMethod($client, 'getCurlConnectTo', array('2606:4700::1111')));
    }

    public function testPinnedSocketRequestKeepsOriginalHost()
    {
        $client = new Client($this->container);

        $this->assertSame(
            array('https://user:secret@93.184.215.14:8443/path/to?query=1&b=2', array('X-Test: 1', 'Host: Example.org:8443'), 'Example.org'),
            $this->invokePrivateMethod($client, 'getPinnedSocketRequest', array(
                'HTTPS://user:secret@Example.org:8443/path/to?query=1&b=2#fragment',
                array('Host: attacker.example', 'X-Test: 1'),
                '93.184.215.14',
            ))
        );

        $this->assertSame(
            array('http://[2606:4700::1111]/', array('Host: example.org.'), 'example.org'),
            $this->invokePrivateMethod($client, 'getPinnedSocketRequest', array('http://example.org.', array(), '2606:4700::1111'))
        );

        $this->assertSame(
            array('http://[2606:4700::1111]:8080/webhook', array('Host: [2606:4700::1111]:8080'), '2606:4700::1111'),
            $this->invokePrivateMethod($client, 'getPinnedSocketRequest', array('http://[2606:4700::1111]:8080/webhook', array(), '2606:4700::1111'))
        );
    }
}
