<?php

require_once __DIR__.'/Base.php';

use Core\Helper;
use Model\Config;

class HelperTest extends Base
{
    public function testMarkdown()
    {
        $h = new Helper($this->container);

        $this->assertEquals('<p>Test</p>', $h->markdown('Test'));

        $this->assertEquals(
            '<p>Task #123</p>',
            $h->markdown('Task #123')
        );

        $this->assertEquals(
            '<p>Task <a href="?controller=a&amp;action=b&amp;c=d&amp;task_id=123">#123</a></p>',
            $h->markdown('Task #123', array('controller' => 'a', 'action' => 'b', 'params' => array('c' => 'd')))
        );

        $this->assertEquals(
            '<p>Check that: <a href="http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454">http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454</a></p>',
            $h->markdown(
                'Check that: http://stackoverflow.com/questions/1732348/regex-match-open-tags-except-xhtml-self-contained-tags/1732454#1732454',
                array('controller' => 'a', 'action' => 'b', 'params' => array('c' => 'd'))
            )
        );
    }

    public function testGetCurrentBaseUrl()
    {
        $h = new Helper($this->container);

        $_SERVER['PHP_SELF'] = '/';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = 1234;

        $this->assertEquals('http://localhost:1234/', $h->getCurrentBaseUrl());

        $c = new Config($this->container);
        $c->save(array('application_url' => 'https://mykanboard/'));
        $this->assertEquals('https://mykanboard/', $c->get('application_url'));
        $this->assertEquals('https://mykanboard/', $h->getCurrentBaseUrl());
    }
}
