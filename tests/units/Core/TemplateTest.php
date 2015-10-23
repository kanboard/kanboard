<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Template;

class TemplateTest extends Base
{
    public function testGetTemplateFile()
    {
        $t = new Template($this->container);
        $this->assertStringEndsWith(
            'app'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Template'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b.php',
            $t->getTemplateFile('a'.DIRECTORY_SEPARATOR.'b')
        );
    }

    public function testGetPluginTemplateFile()
    {
        $t = new Template($this->container);
        $this->assertStringEndsWith(
            'app'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'Myplugin'.DIRECTORY_SEPARATOR.'Template'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b.php',
            $t->getTemplateFile('myplugin:a'.DIRECTORY_SEPARATOR.'b')
        );
    }

    public function testGetOverridedTemplateFile()
    {
        $t = new Template($this->container);
        $t->setTemplateOverride('a'.DIRECTORY_SEPARATOR.'b', 'myplugin:c');

        $this->assertStringEndsWith(
            'app'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'Myplugin'.DIRECTORY_SEPARATOR.'Template'.DIRECTORY_SEPARATOR.'c.php',
            $t->getTemplateFile('a'.DIRECTORY_SEPARATOR.'b')
        );

        $this->assertStringEndsWith(
            'app'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Template'.DIRECTORY_SEPARATOR.'d.php',
            $t->getTemplateFile('d')
        );
    }
}
