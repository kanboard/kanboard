<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Tool;

class ToolTest extends Base
{
    public function testMailboxHash()
    {
        $this->assertEquals('test1', Tool::getMailboxHash('a+test1@localhost'));
        $this->assertEquals('', Tool::getMailboxHash('test1@localhost'));
        $this->assertEquals('', Tool::getMailboxHash('test1'));
    }
}
