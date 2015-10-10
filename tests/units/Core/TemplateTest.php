<?php

require_once __DIR__.'/../Base.php';

use Core\Template;

class TemplateTest extends Base
{
    public function testGetTemplateFile()
    {
        $t = new Template($this->container);
        $this->assertStringEndsWith('app/Core/../Template/a/b.php', $t->getTemplateFile('a/b'));
    }

    public function testGetPluginTemplateFile()
    {
        $t = new Template($this->container);
        $this->assertStringEndsWith('app/Core/../../plugins/Myplugin/Template/a/b.php', $t->getTemplateFile('myplugin:a/b'));
    }

    public function testGetOverridedTemplateFile()
    {
        $t = new Template($this->container);
        $t->setTemplateOverride('a/b', 'myplugin:c');
        $this->assertStringEndsWith('app/Core/../../plugins/Myplugin/Template/c.php', $t->getTemplateFile('a/b'));
        $this->assertStringEndsWith('app/Core/../Template/d.php', $t->getTemplateFile('d'));
    }
}
