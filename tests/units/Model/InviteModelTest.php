<?php

namespace KanboardTests\units\Model;

use KanboardTests\units\Base;
use Kanboard\Model\InviteModel;

#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class InviteModelTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container['emailClient'] = $this
            ->getMockBuilder('\Kanboard\Core\Mail\Client')
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('send'))
            ->getMock();
    }

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
