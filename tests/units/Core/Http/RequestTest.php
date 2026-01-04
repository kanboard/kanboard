<?php

namespace KanboardTests\units\Core\Http;

use KanboardTests\units\Base;
use Kanboard\Core\Http\Request;

class RequestTest extends Base
{
    public function testGetStringParam()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEquals('', $request->getStringParam('myvar'));

        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEquals('default', $request->getStringParam('myvar', 'default'));

        $request = new Request($this->container, array(), array('myvar' => 'myvalue'), array(), array(), array());
        $this->assertEquals('myvalue', $request->getStringParam('myvar'));
    }

    public function testGetIntegerParam()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEquals(0, $request->getIntegerParam('myvar'));

        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEquals(5, $request->getIntegerParam('myvar', 5));

        $request = new Request($this->container, array(), array('myvar' => 'myvalue'), array(), array(), array());
        $this->assertEquals(0, $request->getIntegerParam('myvar'));

        $request = new Request($this->container, array(), array('myvar' => '123'), array(), array(), array());
        $this->assertEquals(123, $request->getIntegerParam('myvar'));
    }

    public function testGetValues()
    {
        $request = new Request($this->container, array(), array(), array('myvar' => 'myvalue'), array(), array());
        $this->assertEmpty($request->getValue('myvar'));

        $request = new Request($this->container, array(), array(), array('myvar' => 'myvalue', 'csrf_token' => $this->container['token']->getCSRFToken()), array(), array());
        $this->assertEquals('myvalue', $request->getValue('myvar'));

        $request = new Request($this->container, array(), array(), array('myvar' => 'myvalue', 'csrf_token' => $this->container['token']->getCSRFToken()), array(), array());
        $this->assertEquals(array('myvar' => 'myvalue'), $request->getValues());

        $request = new Request($this->container, array(), array(), array('myvar' => 'myvalue', '-----------------------------7e1c32510025c--' => '', 'csrf_token' => $this->container['token']->getCSRFToken()), array(), array());
        $this->assertEquals(array('myvar' => 'myvalue'), $request->getValues());
    }

    public function testGetFileContent()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEmpty($request->getFileContent('myfile'));

        $filename = tempnam(sys_get_temp_dir(), 'UnitTest');
        file_put_contents($filename, 'something');

        $request = new Request($this->container, array(), array(), array(), array('myfile' => array('tmp_name' => $filename)), array());
        $this->assertEquals('something', $request->getFileContent('myfile'));

        unlink($filename);
    }

    public function testGetFilePath()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEmpty($request->getFilePath('myfile'));

        $request = new Request($this->container, array(), array(), array(), array('myfile' => array('tmp_name' => 'somewhere')), array());
        $this->assertEquals('somewhere', $request->getFilePath('myfile'));
    }

    public function testIsPost()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertFalse($request->isPost());

        $request = new Request($this->container, array('REQUEST_METHOD' => 'POST'), array(), array(), array(), array());
        $this->assertTrue($request->isPost());
    }

    public function testIsAjax()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertFalse($request->isAjax());

        $request = new Request($this->container, array('HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'), array(), array(), array(), array());
        $this->assertTrue($request->isAjax());
    }

    public function testIsHTTPS()
    {
        $request = new Request($this->container, array(), array(), array(), array());
        $this->assertFalse($request->isHTTPS());

        $request = new Request($this->container, array('HTTPS' => ''), array(), array(), array(), array());
        $this->assertFalse($request->isHTTPS());

        $request = new Request($this->container, array('HTTPS' => 'off'), array(), array(), array(), array());
        $this->assertFalse($request->isHTTPS());

        $request = new Request($this->container, array('HTTPS' => 'on'), array(), array(), array(), array());
        $this->assertTrue($request->isHTTPS());

        $request = new Request($this->container, array('HTTPS' => '1'), array(), array(), array(), array());
        $this->assertTrue($request->isHTTPS());

        $request = new Request($this->container, array('HTTP_X_FORWARDED_PROTO' => 'https'), array(), array(), array(), array());
        $this->assertTrue($request->isHTTPS());

        $request = new Request($this->container, array('HTTP_X_FORWARDED_PROTO' => 'http'), array(), array(), array(), array());
        $this->assertFalse($request->isHTTPS());
    }

    public function testGetCookie()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEmpty($request->getCookie('mycookie'));

        $request = new Request($this->container, array(), array(), array(), array(), array('mycookie' => 'miam'));
        $this->assertEquals('miam', $request->getCookie('mycookie'));
    }

    public function testGetHeader()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEmpty($request->getHeader('X-Forwarded-For'));

        $request = new Request($this->container, array('HTTP_X_FORWARDED_FOR' => 'test'), array(), array(), array(), array());
        $this->assertEquals('test', $request->getHeader('X-Forwarded-For'));
    }

    public function testGetRemoteUserReturnsEmptyWhenHeaderMissing()
    {
        $server = ['REMOTE_ADDR' => '203.0.113.5'];
        $request = new Request($this->container, $server);
        $this->assertEmpty($request->getRemoteUser(['203.0.113.0/24']));
    }

    public function testGetRemoteUserReturnsHeaderValueWhenUsingTrustedProxy()
    {
        $server = [
            'REMOTE_ADDR' => '203.0.113.5',
            REVERSE_PROXY_USER_HEADER => 'test',
        ];
        $request = new Request($this->container, $server);
        $this->assertEquals('test', $request->getRemoteUser(['203.0.113.0/24']));
    }

    public function testGetRemoteUserReturnsEmptyWhenUsingUntrustedProxy()
    {
        $server = [
            'REMOTE_ADDR' => '198.51.100.5',
            REVERSE_PROXY_USER_HEADER => 'test',
        ];
        $request = new Request($this->container, $server);
        $this->assertEmpty($request->getRemoteUser(['203.0.113.0/24']));
    }

    public function testGetRemoteEmailReturnsEmptyWhenHeaderMissing()
    {
        $server = ['REMOTE_ADDR' => '203.0.113.5'];
        $request = new Request($this->container, $server);
        $this->assertEmpty($request->getRemoteEmail(['203.0.113.0/24']));
    }

    public function testGetRemoteEmailReturnsHeaderValueWhenUsingTrustedProxy()
    {
        $server = [
            'REMOTE_ADDR' => '203.0.113.5',
            REVERSE_PROXY_EMAIL_HEADER => 'test@example.com',
        ];
        $request = new Request($this->container, $server);
        $this->assertEquals('test@example.com', $request->getRemoteEmail(['203.0.113.0/24']));
    }

    public function testGetRemoteEmailReturnsEmptyWhenUsingUntrustedProxy()
    {
        $server = [
            'REMOTE_ADDR' => '198.51.100.5',
            REVERSE_PROXY_EMAIL_HEADER => 'test@example.com',
        ];
        $request = new Request($this->container, $server);
        $this->assertEmpty($request->getRemoteEmail(['203.0.113.0/24']));
    }

    public function testGetRemoteNameReturnsEmptyWhenHeaderMissing()
    {
        $server = ['REMOTE_ADDR' => '203.0.113.5'];
        $request = new Request($this->container, $server);
        $this->assertEmpty($request->getRemoteName(['203.0.113.0/24']));
    }

    public function testGetRemoteNameReturnsHeaderValueWhenUsingTrustedProxy()
    {
        $server = [
            'REMOTE_ADDR' => '203.0.113.5',
            REVERSE_PROXY_FULLNAME_HEADER => 'Test Name',
        ];
        $request = new Request($this->container, $server);
        $this->assertEquals('Test Name', $request->getRemoteName(['203.0.113.0/24']));
    }

    public function testGetRemoteNameReturnsEmptyWhenUsingUntrustedProxy()
    {
        $server = [
            'REMOTE_ADDR' => '198.51.100.5',
            REVERSE_PROXY_FULLNAME_HEADER => 'Test Name',
        ];
        $request = new Request($this->container, $server);
        $this->assertEmpty($request->getRemoteName(['203.0.113.0/24']));
    }

    public function testGetQueryString()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEmpty($request->getQueryString());

        $request = new Request($this->container, array('QUERY_STRING' => 'k=v'), array(), array(), array(), array());
        $this->assertEquals('k=v', $request->getQueryString());
    }

    public function testGetUri()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEmpty($request->getUri());

        $request = new Request($this->container, array('REQUEST_URI' => '/blah'), array(), array(), array(), array());
        $this->assertEquals('/blah', $request->getUri());
    }

    public function testGetUserAgent()
    {
        $request = new Request($this->container, array(), array(), array(), array(), array());
        $this->assertEquals('Unknown', $request->getUserAgent());

        $request = new Request($this->container, array('HTTP_USER_AGENT' => 'My browser'), array(), array(), array(), array());
        $this->assertEquals('My browser', $request->getUserAgent());
    }

    public function testGetIpAddressReturnsEmptyStringWhenNoRemoteAddr()
    {
        $request = new Request($this->container);
        $this->assertEquals('', $request->getIpAddress());
    }

    public function testGetIpAddressReturnsRemoteAddrByDefault()
    {
        $request = new Request($this->container, ['REMOTE_ADDR' => '203.0.113.10']);
        $this->assertEquals('203.0.113.10', $request->getIpAddress());
    }

    public function testGetIpAddressUsesTrustedProxyHeaderWhenValid()
    {
        $server = [
            'REMOTE_ADDR' => '203.0.113.5',
            'HTTP_X_FORWARDED_FOR' => 'invalid, 198.51.100.42, 198.51.100.52',
        ];
        $request = new Request($this->container, $server);

        $this->assertEquals(
            '198.51.100.42',
            $request->getIpAddress(['HTTP_X_FORWARDED_FOR'], ['203.0.113.0/24'])
        );
    }

    public function testGetIpAddressFallsBackToRemoteAddrWhenHeaderInvalid()
    {
        $server = [
            'REMOTE_ADDR' => '203.0.113.5',
            'HTTP_X_FORWARDED_FOR' => 'still-invalid',
        ];
        $request = new Request($this->container, $server);

        $this->assertEquals(
            '203.0.113.5',
            $request->getIpAddress(['HTTP_X_FORWARDED_FOR'], ['203.0.113.0/24'])
        );
    }

    public function testGetIpAddressSupportsIPv6ForwardedValues()
    {
        $server = [
            'REMOTE_ADDR' => '2001:db8::10',
            'HTTP_X_FORWARDED_FOR' => '2001:db8::abcd',
        ];
        $request = new Request($this->container, $server);

        $this->assertEquals(
            '2001:db8::abcd',
            $request->getIpAddress(['HTTP_X_FORWARDED_FOR'], ['2001:db8::/32'])
        );
    }

    public function testGetIpAddressIgnoresUntrustedProxyHeaders()
    {
        $server = [
            'REMOTE_ADDR' => '198.51.100.1',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.77',
        ];
        $request = new Request($this->container, $server);

        $this->assertEquals(
            '198.51.100.1',
            $request->getIpAddress(['HTTP_X_FORWARDED_FOR'], ['203.0.113.0/24'])
        );
    }

    public function testIsTrustedProxyWithIPv4Networks()
    {
        $request = new Request($this->container, ['REMOTE_ADDR' => '203.0.113.5']);

        $this->assertTrue($request->isTrustedProxy(['203.0.113.0/24']));
        $this->assertFalse($request->isTrustedProxy(['198.51.100.0/24']));
    }

    public function testIsTrustedProxyWithIPv6Networks()
    {
        $request = new Request($this->container, ['REMOTE_ADDR' => '2001:db8::abcd']);

        $this->assertTrue($request->isTrustedProxy(['2001:db8::/32']));
        $this->assertFalse($request->isTrustedProxy(['2001:db9::/32']));
    }

    public function testIsTrustedProxyWithIPv4Loopback()
    {
        $request = new Request($this->container, ['REMOTE_ADDR' => '127.0.0.1']);

        $this->assertTrue($request->isTrustedProxy(['127.0.0.0/8']));
        $this->assertFalse($request->isTrustedProxy(['10.0.0.0/8']));
    }

    public function testIsTrustedProxyWithIPv6Loopback()
    {
        $request = new Request($this->container, ['REMOTE_ADDR' => '::1']);

        $this->assertTrue($request->isTrustedProxy(['::1/128']));
        $this->assertFalse($request->isTrustedProxy(['2001:db8::/128']));
    }
}
