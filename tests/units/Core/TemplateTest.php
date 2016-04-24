<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Template;

class TemplateTest extends Base
{
    public function testGetTemplateFile()
    {
        $template = new Template($this->container['helper']);

        $this->assertStringEndsWith(
            implode(DIRECTORY_SEPARATOR, array('app', 'Core', '..', 'Template', 'a', 'b.php')),
            $template->getTemplateFile('a'.DIRECTORY_SEPARATOR.'b')
        );

        $this->assertStringEndsWith(
            implode(DIRECTORY_SEPARATOR, array('app', 'Core', '..', 'Template', 'a', 'b.php')),
            $template->getTemplateFile('kanboard:a'.DIRECTORY_SEPARATOR.'b')
        );
    }

    public function testGetPluginTemplateFile()
    {
        $template = new Template($this->container['helper']);
        $this->assertStringEndsWith(
            implode(DIRECTORY_SEPARATOR, array(PLUGINS_DIR, 'Myplugin', 'Template', 'a', 'b.php')),
            $template->getTemplateFile('myplugin:a'.DIRECTORY_SEPARATOR.'b')
        );
    }

    public function testGetOverridedTemplateFile()
    {
        $template = new Template($this->container['helper']);
        $template->setTemplateOverride('a'.DIRECTORY_SEPARATOR.'b', 'myplugin:c');

        $this->assertStringEndsWith(
            implode(DIRECTORY_SEPARATOR, array(PLUGINS_DIR, 'Myplugin', 'Template', 'c.php')),
            $template->getTemplateFile('a'.DIRECTORY_SEPARATOR.'b')
        );

        $this->assertStringEndsWith(
            implode(DIRECTORY_SEPARATOR, array('app', 'Core', '..', 'Template', 'd.php')),
            $template->getTemplateFile('d')
        );
    }
}
