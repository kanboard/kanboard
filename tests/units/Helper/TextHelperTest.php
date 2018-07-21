<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\TextHelper;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\UserModel;

class TextHelperTest extends Base
{
    public function testImplode()
    {
        $textHelper = new TextHelper($this->container);
        $html = '&lt;img src=x onerror=alert(0)&gt;';
        $this->assertEquals($html, $textHelper->implode(', ', array('<img src=x onerror=alert(0)>')));
    }

    public function testMarkdownTaskLink()
    {
        $textHelper = new TextHelper($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertTrue($projectModel->enablePublicAccess(1));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1)));
        $project = $projectModel->getById(1);

        $this->assertEquals('<p>Test</p>', $textHelper->markdown('Test'));

        $this->assertEquals(
            '<p>Task <a href="?controller=TaskViewController&amp;action=show&amp;task_id=123">#123</a></p>',
            $textHelper->markdown('Task #123')
        );

        $this->assertEquals(
            '<p>Task #123</p>',
            $textHelper->markdown('Task #123', true)
        );

        $this->assertEquals(
            '<p>Task <a href="http://localhost/?controller=TaskViewController&amp;action=readonly&amp;token='.$project['token'].'&amp;task_id=1">#1</a></p>',
            $textHelper->markdown('Task #1', true)
        );

        $this->assertEquals(
            '<p>Check that: <a href="http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454">http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454</a></p>',
            $textHelper->markdown(
                'Check that: http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454'
            )
        );

        $this->assertEquals(
            '<p><a href="http://localhost">item #123 is here</a></p>',
            $textHelper->markdown(
                '[item #123 is here](http://localhost)'
            )
        );
    }

    public function testMarkdownUserLink()
    {
        $textHelper = new TextHelper($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'firstname.lastname', 'name' => 'Firstname Lastname')));

        $this->assertEquals(
            '<p>Text <a href="?controller=UserViewController&amp;action=profile&amp;user_id=1" class="user-mention-link" title="admin">@admin</a> @notfound</p>',
            $textHelper->markdown('Text @admin @notfound')
        );

        $this->assertEquals(
            '<p>Text <a href="?controller=UserViewController&amp;action=profile&amp;user_id=1" class="user-mention-link" title="admin">@admin</a>,</p>',
            $textHelper->markdown('Text @admin,')
        );

        $this->assertEquals(
            '<p>Text <a href="?controller=UserViewController&amp;action=profile&amp;user_id=1" class="user-mention-link" title="admin">@admin</a>!</p>',
            $textHelper->markdown('Text @admin!')
        );

        $this->assertEquals(
            '<p>Text <a href="?controller=UserViewController&amp;action=profile&amp;user_id=1" class="user-mention-link" title="admin">@admin</a>? </p>',
            $textHelper->markdown('Text @admin? ')
        );

        $this->assertEquals(
            '<p>Text <a href="?controller=UserViewController&amp;action=profile&amp;user_id=1" class="user-mention-link" title="admin">@admin</a>.</p>',
            $textHelper->markdown('Text @admin.')
        );

        $this->assertEquals(
            '<p>Text <a href="?controller=UserViewController&amp;action=profile&amp;user_id=1" class="user-mention-link" title="admin">@admin</a>: test</p>',
            $textHelper->markdown('Text @admin: test')
        );

        $this->assertEquals(
            '<p>Text <a href="?controller=UserViewController&amp;action=profile&amp;user_id=1" class="user-mention-link" title="admin">@admin</a>: test</p>',
            $textHelper->markdown('Text @admin: test')
        );

        $this->assertEquals(
            '<p>Text <a href="?controller=UserViewController&amp;action=profile&amp;user_id=2" class="user-mention-link" title="Firstname Lastname">@firstname.lastname</a>. test</p>',
            $textHelper->markdown('Text @firstname.lastname. test')
        );

        $this->assertEquals('<p>Text @admin @notfound</p>', $textHelper->markdown('Text @admin @notfound', true));

        $this->assertEquals(
            '<p><a href="http://localhost">mention @admin at localhost</a></p>',
            $textHelper->markdown(
                '[mention @admin at localhost](http://localhost)'
            )
        );
    }

    public function testFormatBytes()
    {
        $textHelper = new TextHelper($this->container);

        $this->assertEquals('0', $textHelper->bytes(0));
        $this->assertEquals('1k', $textHelper->bytes(1024));
        $this->assertEquals('33.71k', $textHelper->bytes(34520));
    }

    public function testContains()
    {
        $textHelper = new TextHelper($this->container);

        $this->assertTrue($textHelper->contains('abc', 'b'));
        $this->assertFalse($textHelper->contains('abc', 'd'));
    }

    public function testInList()
    {
        $textHelper = new TextHelper($this->container);
        $this->assertEquals('?', $textHelper->in('a', array('b' => 'c')));
        $this->assertEquals('c', $textHelper->in('b', array('b' => 'c')));
    }
}
