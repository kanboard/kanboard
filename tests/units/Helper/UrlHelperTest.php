<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\UrlHelper;
use Kanboard\Model\ConfigModel;
use Kanboard\Core\Http\Request;

class UrlHelperTest extends Base
{
    public function testPluginLink()
    {
        $h = new UrlHelper($this->container);
        $this->assertEquals(
            '<a href="?controller=a&amp;action=b&amp;d=e&amp;plugin=something" class="f" title=\'g\' target="_blank">label</a>',
            $h->link('label', 'a', 'b', array('d' => 'e', 'plugin' => 'something'), false, 'f', 'g', true)
        );
    }

    public function testPluginLinkWithRouteDefined()
    {
        $this->container['route']->enable();
        $this->container['route']->addRoute('/myplugin/something/:d', 'a', 'b', 'something');

        $h = new UrlHelper($this->container);
        $this->assertEquals(
            '<a href="myplugin/something/e" class="f" title=\'g\' target="_blank">label</a>',
            $h->link('label', 'a', 'b', array('d' => 'e', 'plugin' => 'something'), false, 'f', 'g', true)
        );
    }

    public function testAppLink()
    {
        $h = new UrlHelper($this->container);
        $this->assertEquals(
            '<a href="?controller=a&amp;action=b&amp;d=e" class="f" title=\'g\' target="_blank">label</a>',
            $h->link('label', 'a', 'b', array('d' => 'e'), false, 'f', 'g', true)
        );
    }

    public function testHref()
    {
        $h = new UrlHelper($this->container);
        $this->assertEquals(
            '?controller=a&amp;action=b&amp;d=e',
            $h->href('a', 'b', array('d' => 'e'))
        );
    }

    public function testTo()
    {
        $h = new UrlHelper($this->container);
        $this->assertEquals(
            '?controller=a&action=b&d=e',
            $h->to('a', 'b', array('d' => 'e'))
        );
    }

    public function testDir()
    {
        $this->container['request'] = new Request($this->container, array(
                'PHP_SELF' => '/kanboard/index.php',
                'REQUEST_METHOD' => 'GET'
            )
        );

        $h = new UrlHelper($this->container);
        $this->assertEquals('/kanboard/', $h->dir());

        $this->container['request'] = new Request($this->container, array(
                'PHP_SELF' => '/index.php',
                'REQUEST_METHOD' => 'GET'
            )
        );

        $h = new UrlHelper($this->container);
        $this->assertEquals('/', $h->dir());
    }

    public function testServer()
    {
        $this->container['request'] = new Request($this->container, array(
                'PHP_SELF' => '/index.php',
                'REQUEST_METHOD' => 'GET',
                'SERVER_NAME' => 'localhost',
                'SERVER_PORT' => 80,
            )
        );

        $h = new UrlHelper($this->container);
        $this->assertEquals('http://localhost/', $h->server());

        $this->container['request'] = new Request($this->container, array(
                'PHP_SELF' => '/index.php',
                'REQUEST_METHOD' => 'GET',
                'SERVER_NAME' => 'kb',
                'SERVER_PORT' => 1234,
            )
        );

        $h = new UrlHelper($this->container);
        $this->assertEquals('http://kb:1234/', $h->server());
    }

    public function testBase()
    {
        $this->container['request'] = new Request($this->container, array(
                'PHP_SELF' => '/index.php',
                'REQUEST_METHOD' => 'GET',
                'SERVER_NAME' => 'kb',
                'SERVER_PORT' => 1234,
            )
        );

        $h = new UrlHelper($this->container);
        $this->assertEquals('http://kb:1234/', $h->base());

        $c = new ConfigModel($this->container);
        $c->save(array('application_url' => 'https://mykanboard/'));
        $this->container['memoryCache']->flush();

        $h = new UrlHelper($this->container);
        $this->assertEquals('https://mykanboard/', $c->get('application_url'));
        $this->assertEquals('https://mykanboard/', $h->base());
    }
}
