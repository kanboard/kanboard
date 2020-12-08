<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\ExternalLink\ExternalLinkManager;
use Kanboard\ExternalLink\WebLinkProvider;
use Kanboard\ExternalLink\AttachmentLinkProvider;

class ExternalLinkManagerTest extends Base
{
    public function testRegister()
    {
        $externalLinkManager = new ExternalLinkManager($this->container);
        $webLinkProvider = new WebLinkProvider($this->container);
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);

        $externalLinkManager->register($webLinkProvider);
        $externalLinkManager->register($attachmentLinkProvider);

        $this->assertInstanceOf(get_class($webLinkProvider), $externalLinkManager->getProvider($webLinkProvider->getType()));
        $this->assertInstanceOf(get_class($attachmentLinkProvider), $externalLinkManager->getProvider($attachmentLinkProvider->getType()));
    }

    public function testGetProviderNotFound()
    {
        $externalLinkManager = new ExternalLinkManager($this->container);

        $this->expectException('\Kanboard\Core\ExternalLink\ExternalLinkProviderNotFound');
        $externalLinkManager->getProvider('not found');
    }

    public function testGetTypes()
    {
        $externalLinkManager = new ExternalLinkManager($this->container);
        $webLinkProvider = new WebLinkProvider($this->container);
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);

        $this->assertEquals(array(ExternalLinkManager::TYPE_AUTO => 'Auto'), $externalLinkManager->getTypes());

        $externalLinkManager->register($webLinkProvider);
        $externalLinkManager->register($attachmentLinkProvider);

        $this->assertEquals(
            array(ExternalLinkManager::TYPE_AUTO => 'Auto', 'attachment' => 'Attachment', 'weblink' => 'Web Link'),
            $externalLinkManager->getTypes()
        );
    }

    public function testGetDependencyLabel()
    {
        $externalLinkManager = new ExternalLinkManager($this->container);
        $webLinkProvider = new WebLinkProvider($this->container);
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);

        $externalLinkManager->register($webLinkProvider);
        $externalLinkManager->register($attachmentLinkProvider);

        $this->assertSame('Related', $externalLinkManager->getDependencyLabel($webLinkProvider->getType(), 'related'));
        $this->assertSame('custom', $externalLinkManager->getDependencyLabel($webLinkProvider->getType(), 'custom'));
    }

    public function testFindProviderNotFound()
    {
        $externalLinkManager = new ExternalLinkManager($this->container);
        $webLinkProvider = new WebLinkProvider($this->container);
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);

        $externalLinkManager->register($webLinkProvider);
        $externalLinkManager->register($attachmentLinkProvider);

        $this->expectException('\Kanboard\Core\ExternalLink\ExternalLinkProviderNotFound');
        $externalLinkManager->find();
    }

    public function testFindProvider()
    {
        $externalLinkManager = new ExternalLinkManager($this->container);
        $webLinkProvider = new WebLinkProvider($this->container);
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);

        $externalLinkManager->register($webLinkProvider);
        $externalLinkManager->register($attachmentLinkProvider);

        $externalLinkManager->setUserInput(array('text' => 'https://google.com/', 'type' => ExternalLinkManager::TYPE_AUTO));
        $this->assertSame($webLinkProvider, $externalLinkManager->find());

        $externalLinkManager->setUserInput(array('text' => 'https://google.com/file.pdf', 'type' => ExternalLinkManager::TYPE_AUTO));
        $this->assertSame($attachmentLinkProvider, $externalLinkManager->find());
    }

    public function testFindProviderWithSelectedType()
    {
        $externalLinkManager = new ExternalLinkManager($this->container);
        $webLinkProvider = new WebLinkProvider($this->container);
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);

        $externalLinkManager->register($webLinkProvider);
        $externalLinkManager->register($attachmentLinkProvider);

        $externalLinkManager->setUserInput(array('text' => 'https://google.com/', 'type' => $webLinkProvider->getType()));
        $this->assertSame($webLinkProvider, $externalLinkManager->find());

        $externalLinkManager->setUserInput(array('text' => 'https://google.com/file.pdf', 'type' => $attachmentLinkProvider->getType()));
        $this->assertSame($attachmentLinkProvider, $externalLinkManager->find());
    }

    public function testFindProviderWithSelectedTypeNotFound()
    {
        $externalLinkManager = new ExternalLinkManager($this->container);
        $webLinkProvider = new WebLinkProvider($this->container);
        $attachmentLinkProvider = new AttachmentLinkProvider($this->container);

        $externalLinkManager->register($webLinkProvider);
        $externalLinkManager->register($attachmentLinkProvider);

        $this->expectException('\Kanboard\Core\ExternalLink\ExternalLinkProviderNotFound');
        $externalLinkManager->setUserInput(array('text' => 'https://google.com/', 'type' => 'not found'));
        $externalLinkManager->find();
    }
}
