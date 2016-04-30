<?php

require_once __DIR__.'/../Base.php';

use Kanboard\ExternalLink\AttachmentLinkProvider;

class AttachmentLinkProviderTest extends Base
{
    public function testGetName()
    {
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);
        $this->assertEquals('Attachment', $attachmentLinkProvider->getName());
    }

    public function testGetType()
    {
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);
        $this->assertEquals('attachment', $attachmentLinkProvider->getType());
    }

    public function testGetDependencies()
    {
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);
        $this->assertEquals(array('related' => 'Related'), $attachmentLinkProvider->getDependencies());
    }

    public function testMatch()
    {
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);

        $attachmentLinkProvider->setUserTextInput('https://kanboard.net/FILE.DOC');
        $this->assertTrue($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('https://kanboard.net/folder/document.PDF');
        $this->assertTrue($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('https://kanboard.net/archive.zip');
        $this->assertTrue($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('  https://kanboard.net/folder/archive.tar ');
        $this->assertTrue($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('http:// invalid url');
        $this->assertFalse($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('');
        $this->assertFalse($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('https://kanboard.net/folder/document.html');
        $this->assertFalse($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('https://kanboard.net/folder/DOC.HTML');
        $this->assertFalse($attachmentLinkProvider->match());

        $attachmentLinkProvider->setUserTextInput('https://kanboard.net/folder/document.do');
        $this->assertFalse($attachmentLinkProvider->match());
    }

    public function testGetLink()
    {
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);
        $this->assertInstanceOf('\Kanboard\ExternalLink\AttachmentLink', $attachmentLinkProvider->getLink());
    }
}
