<?php

namespace KanboardTests\units\ExternalLink;

use KanboardTests\units\Base;
use Kanboard\Core\Http\Client;
use Kanboard\ExternalLink\WebLink;
use Psr\Log\NullLogger;

class WebLinkTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container['httpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('get', 'getJson', 'postJson', 'postJsonAsync', 'postForm', 'postFormAsync', 'withPrivateNetworksBlocked'))
            ->getMock();

        $this->container['blockedHttpClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Http\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('get'))
            ->getMock();

        $this->container['httpClient']
            ->method('withPrivateNetworksBlocked')
            ->willReturn($this->container['blockedHttpClient']);

        $this->container['httpClient']
            ->expects($this->never())
            ->method('get');
    }

    public function testGetTitleFromHtml()
    {
        $url = 'https://kanboard.org/something';
        $title = 'My title';
        $html = '<!DOCTYPE html><html><head><title>  '.$title.'  </title></head><body>Test</body></html>';

        $webLink = new WebLink($this->container);
        $webLink->setUrl($url);
        $this->assertEquals($url, $webLink->getUrl());

        $this->container['blockedHttpClient']
            ->expects($this->once())
            ->method('get')
            ->with($url, array(), false, false)
            ->willReturn($html);

        $this->assertEquals($title, $webLink->getTitle());
    }

    public function testGetTitleFromUrl()
    {
        $url = 'https://kanboard.org/something';
        $html = '<!DOCTYPE html><html><head></head><body>Test</body></html>';

        $webLink = new WebLink($this->container);
        $webLink->setUrl($url);
        $this->assertEquals($url, $webLink->getUrl());

        $this->container['blockedHttpClient']
            ->expects($this->once())
            ->method('get')
            ->with($url, array(), false, false)
            ->willReturn($html);

        $this->assertEquals('kanboard.org/something', $webLink->getTitle());
    }

    public function testGetTitleFromPrivateNetworkUrl()
    {
        $this->container['blockedHttpClient']
            ->expects($this->never())
            ->method('get');

        $this->container['logger'] = new NullLogger();
        $this->container['httpClient'] = new Client($this->container);

        foreach (array('http://192.168.0.1/test', 'http://[::ffff:127.0.0.1]:9/test', 'http://0x7f000001:9/test') as $url) {
            $webLink = new WebLink($this->container);
            $webLink->setUrl($url);

            $components = parse_url($url);
            $this->assertEquals($components['host'].$components['path'], $webLink->getTitle());
        }
    }
}
