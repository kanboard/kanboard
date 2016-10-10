<?php

require_once __DIR__.'/../Base.php';

use Kanboard\ExternalLink\FileLinkProvider;

class FileLinkProviderTest extends Base
{
    public function testGetName()
    {
        $attachmentLinkProvider = new FileLinkProvider($this->container);
        $this->assertEquals('Local File', $attachmentLinkProvider->getName());
    }

    public function testGetType()
    {
        $attachmentLinkProvider = new FileLinkProvider($this->container);
        $this->assertEquals('file', $attachmentLinkProvider->getType());
    }

    public function testGetDependencies()
    {
        $attachmentLinkProvider = new FileLinkProvider($this->container);
        $this->assertEquals(array('related' => 'Related'), $attachmentLinkProvider->getDependencies());
    }

    public function testMatch()
    {
        $attachmentLinkProvider = new FileLinkProvider($this->container);

        $attachmentLinkProvider->setUserTextInput('file:///tmp/test.txt');
        $this->assertTrue($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('owncloud:///tmp/test.txt');
        $this->assertTrue($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('notebooks:///tmp/test.txt');
        $this->assertTrue($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('http://google.com/');
        $this->assertFalse($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('https://google.com/');
        $this->assertFalse($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('ftp://google.com/');
        $this->assertFalse($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('');
        $this->assertFalse($attachmentLinkProvider->match());
    }

    public function testGetLink()
    {
        $attachmentLinkProvider = new FileLinkProvider($this->container);
        $this->assertInstanceOf('\Kanboard\ExternalLink\FileLink', $attachmentLinkProvider->getLink());
    }
}
