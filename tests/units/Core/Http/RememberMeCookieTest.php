<?php

namespace Kanboard\Core\Http;

use KanboardTests\units\Core\Http\RememberMeCookieTest;

function setcookie($name, $value = "", $expire = 0, $path = "", $domain = "", $secure = false, $httponly = false)
{
    return RememberMeCookieTest::$functions->setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

namespace KanboardTests\units\Core\Http;

use Kanboard\Core\Http\RememberMeCookie;
use Kanboard\Core\Http\Request;
use KanboardTests\units\Base;

class RememberMeCookieTest extends Base
{
    public static $functions;

    protected function setUp(): void
    {
        parent::setup();

        self::$functions = $this
            ->getMockBuilder('stdClass')
            ->setMethods(array(
                'setcookie',
            ))
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::$functions = null;
    }

    public function testEncode()
    {
        $cookie = new RememberMeCookie($this->container);
        $this->assertEquals('a|b', $cookie->encode('a', 'b'));
    }

    public function testDecode()
    {
        $cookie = new RememberMeCookie($this->container);
        $this->assertEquals(array('token' => 'a', 'sequence' => 'b'), $cookie->decode('a|b'));
    }

    public function testHasCookie()
    {
        $this->container['request'] = new Request($this->container, array(), array(), array(), array(), array());

        $cookie = new RememberMeCookie($this->container);
        $this->assertFalse($cookie->hasCookie());

        $this->container['request'] = new Request($this->container, array(), array(), array(), array(), array(RememberMeCookie::COOKIE_NAME => 'miam'));
        $this->assertTrue($cookie->hasCookie());
    }

    public function testWrite()
    {
        self::$functions
            ->expects($this->once())
            ->method('setcookie')
            ->with(
                RememberMeCookie::COOKIE_NAME,
                'myToken|mySequence',
                1234,
                '',
                '',
                false,
                true
            )
            ->will($this->returnValue(true));

        $cookie = new RememberMeCookie($this->container);
        $this->assertTrue($cookie->write('myToken', 'mySequence', 1234));
    }

    public function testRead()
    {
        $this->container['request'] = new Request($this->container, array(), array(), array(), array(), array());

        $cookie = new RememberMeCookie($this->container);
        $this->assertFalse($cookie->read());

        $this->container['request'] = new Request($this->container, array(), array(), array(), array(), array(RememberMeCookie::COOKIE_NAME => 'T|S'));

        $this->assertEquals(array('token' => 'T', 'sequence' => 'S'), $cookie->read());
    }

    public function testRemove()
    {
        self::$functions
            ->expects($this->once())
            ->method('setcookie')
            ->with(
                RememberMeCookie::COOKIE_NAME,
                '',
                time() - 3600,
                '',
                '',
                false,
                true
            )
            ->will($this->returnValue(true));

        $cookie = new RememberMeCookie($this->container);
        $this->assertTrue($cookie->remove());
    }
}
