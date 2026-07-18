<?php

namespace KanboardTests\units\Core;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Role;
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

    public function testCommentReplyEscapesTemplateClosingTags()
    {
        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
        );

        $html = $this->container['template']->render('comment/show', array(
            'comment' => array(
                'id' => 1,
                'user_id' => 1,
                'username' => 'alice',
                'name' => 'Alice',
                'email' => '',
                'avatar_path' => '',
                'date_creation' => 0,
                'date_modification' => 0,
                'visibility' => Role::APP_USER,
                'comment' => '</textarea></template><base href="http://127.0.0.1:8899/">',
            ),
            'task' => array('id' => 1),
            'editable' => false,
            'hide_actions' => true,
        ));

        $this->assertStringNotContainsString(
            '<base href="http://127.0.0.1:8899/">',
            $html
        );
        $this->assertStringContainsString(
            '&lt;/textarea&gt;&lt;/template&gt;&lt;base href=&quot;http://127.0.0.1:8899/&quot;&gt;',
            $html
        );
    }
}
