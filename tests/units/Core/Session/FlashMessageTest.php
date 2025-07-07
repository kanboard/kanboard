<?php

namespace KanboardTests\units\Core\Session;

use KanboardTests\units\Base;
use Kanboard\Core\Session\FlashMessage;

class FlashMessageTest extends Base
{
    public function testMessage()
    {
        $flash = new FlashMessage($this->container);

        $flash->success('my message');
        $this->assertEquals('my message', $flash->getMessage('success'));
        $this->assertEmpty($flash->getMessage('success'));

        $flash->failure('my error message');
        $this->assertEquals('my error message', $flash->getMessage('failure'));
        $this->assertEmpty($flash->getMessage('failure'));

        $this->assertEmpty($flash->getMessage('not found'));
    }
}
