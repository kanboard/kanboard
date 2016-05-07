<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Timezone;

class TimezoneTest extends Base
{
    public function testGetTimezones()
    {
        $timezoneModel = new Timezone($this->container);
        $this->assertNotEmpty($timezoneModel->getTimezones());
        $this->assertArrayHasKey('Europe/Paris', $timezoneModel->getTimezones());
        $this->assertContains('Europe/Paris', $timezoneModel->getTimezones());
        $this->assertArrayNotHasKey('', $timezoneModel->getTimezones());

        $this->assertArrayHasKey('', $timezoneModel->getTimezones(true));
        $this->assertContains('Application default', $timezoneModel->getTimezones(true));
    }

    public function testGetCurrentTimezone()
    {
        $timezoneModel = new Timezone($this->container);
        $this->assertEquals('UTC', $timezoneModel->getCurrentTimezone());

        $this->container['sessionStorage']->user = array('timezone' => 'Europe/Paris');
        $this->assertEquals('Europe/Paris', $timezoneModel->getCurrentTimezone());

        $this->container['sessionStorage']->user = array('timezone' => 'Something');
        $this->assertEquals('Something', $timezoneModel->getCurrentTimezone());
    }
}
