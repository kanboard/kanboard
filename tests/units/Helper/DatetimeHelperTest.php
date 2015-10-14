<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\Dt;

class DatetimeHelperTest extends Base
{
    public function testAge()
    {
        $h = new Dt($this->container);

        $this->assertEquals('&lt;15m', $h->age(0, 30));
        $this->assertEquals('&lt;30m', $h->age(0, 1000));
        $this->assertEquals('&lt;1h', $h->age(0, 3000));
        $this->assertEquals('~2h', $h->age(0, 2*3600));
        $this->assertEquals('1d', $h->age(0, 30*3600));
        $this->assertEquals('2d', $h->age(0, 65*3600));
    }

    public function testGetDayHours()
    {
        $h = new Dt($this->container);

        $slots = $h->getDayHours();

        $this->assertNotEmpty($slots);
        $this->assertCount(48, $slots);
        $this->assertArrayHasKey('00:00', $slots);
        $this->assertArrayHasKey('00:30', $slots);
        $this->assertArrayHasKey('01:00', $slots);
        $this->assertArrayHasKey('01:30', $slots);
        $this->assertArrayHasKey('23:30', $slots);
        $this->assertArrayNotHasKey('24:00', $slots);
    }

    public function testGetWeekDays()
    {
        $h = new Dt($this->container);

        $slots = $h->getWeekDays();

        $this->assertNotEmpty($slots);
        $this->assertCount(7, $slots);
        $this->assertContains('Monday', $slots);
        $this->assertContains('Sunday', $slots);
    }

    public function testGetWeekDay()
    {
        $h = new Dt($this->container);

        $this->assertEquals('Monday', $h->getWeekDay(1));
        $this->assertEquals('Sunday', $h->getWeekDay(7));
    }
}
