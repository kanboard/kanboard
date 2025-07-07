<?php

namespace KanboardTests\units\Model;

use KanboardTests\units\Base;
use Kanboard\Model\InviteModel;

class InviteModelTest extends Base
{
    public function testCreation()
    {
        $inviteModel = new InviteModel($this->container);

        $this->container['emailClient']
            ->expects($this->exactly(2))
            ->method('send');

        $inviteModel->createInvites(array('user@domain1.tld', '', 'user@domain2.tld'), 1);
    }

    public function testRemove()
    {
        $inviteModel = new InviteModel($this->container);
        $inviteModel->createInvites(array('user@domain1.tld', 'user@domain2.tld'), 0);
        $this->assertTrue($inviteModel->remove('user@domain1.tld'));
        $this->assertFalse($inviteModel->remove('foobar'));
    }
}
