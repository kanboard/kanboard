<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\MailHelper;

class MailHelperTest extends Base
{
    public function testMailboxHash()
    {
        $helper = new MailHelper($this->container);
        $this->assertEquals('test1', $helper->getMailboxHash('a+test1@localhost'));
        $this->assertEquals('', $helper->getMailboxHash('test1@localhost'));
        $this->assertEquals('', $helper->getMailboxHash('test1'));
    }

    public function testFilterSubject()
    {
        $helper = new MailHelper($this->container);
        $this->assertEquals('Test', $helper->filterSubject('Test'));
        $this->assertEquals('Test', $helper->filterSubject('RE: Test'));
        $this->assertEquals('Test', $helper->filterSubject('FW: Test'));
    }
}
