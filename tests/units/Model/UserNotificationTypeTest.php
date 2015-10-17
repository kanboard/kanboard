<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\UserNotificationType;

class UserNotificationTypeTest extends Base
{
    public function testGetTypes()
    {
        $nt = new UserNotificationType($this->container);
        $this->assertEmpty($nt->getTypes());

        $nt->setType('email', 'Email', 'Something');
        $nt->setType('web', 'Web', 'Something');
        $this->assertEquals(array('email' => 'Email', 'web' => 'Web'), $nt->getTypes());
    }

    public function testGetSelectedTypes()
    {
        $nt = new UserNotificationType($this->container);
        $types = $nt->getSelectedTypes(1);
        $this->assertEmpty($types);

        $this->assertTrue($nt->saveSelectedTypes(1, array('email', 'web')));
        $types = $nt->getSelectedTypes(1);
        $this->assertNotEmpty($types);
        $this->assertEquals(array('email', 'web'), $types);
    }
}
