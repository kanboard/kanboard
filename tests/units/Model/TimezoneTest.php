<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TimezoneModel;

class TimezoneTest extends Base
{
    public function testGetTimezones()
    {
        $timezoneModel = new TimezoneModel($this->container);
        $this->assertNotEmpty($timezoneModel->getTimezones());
        $this->assertArrayHasKey('Europe/Paris', $timezoneModel->getTimezones());
        $this->assertContains('Europe/Paris', $timezoneModel->getTimezones());
        $this->assertArrayNotHasKey('', $timezoneModel->getTimezones());

        $this->assertArrayHasKey('', $timezoneModel->getTimezones(true));
        $this->assertContains('Application default', $timezoneModel->getTimezones(true));
    }

    public function testGetCurrentTimezone()
    {
        $timezoneModel = new TimezoneModel($this->container);
        $this->assertEquals('UTC', $timezoneModel->getCurrentTimezone());

        $_SESSION['user'] = array('timezone' => 'Europe/Paris');
        $this->assertEquals('Europe/Paris', $timezoneModel->getCurrentTimezone());

        $_SESSION['user'] = array('timezone' => 'Something');
        $this->assertEquals('Something', $timezoneModel->getCurrentTimezone());
    }
}
