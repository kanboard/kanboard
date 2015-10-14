<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\NotificationType;

class NotificationTypeTest extends Base
{
    public function testGetTypes()
    {
        $nt = new NotificationType($this->container);
        $this->assertEquals(array('email' => 'Email', 'web' => 'Web'), $nt->getTypes());
    }

    public function testGetUserNotificationTypes()
    {
        $nt = new NotificationType($this->container);
        $types = $nt->getUserSelectedTypes(1);
        $this->assertEmpty($types);

        $this->assertTrue($nt->saveUserSelectedTypes(1, array('email', 'web')));
        $types = $nt->getUserSelectedTypes(1);
        $this->assertNotEmpty($types);
        $this->assertEquals(array('email', 'web'), $types);
    }
}
