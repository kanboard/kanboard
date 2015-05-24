<?php

require_once __DIR__.'/Base.php';

use Helper\Datetime;

class DatetimeHelperTest extends Base
{
    public function testGetDayHours()
    {
        $h = new Datetime($this->container);

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
        $h = new Datetime($this->container);

        $slots = $h->getWeekDays();

        $this->assertNotEmpty($slots);
        $this->assertCount(7, $slots);
        $this->assertContains('Monday', $slots);
        $this->assertContains('Sunday', $slots);
    }

    public function testGetWeekDay()
    {
        $h = new Datetime($this->container);

        $this->assertEquals('Monday', $h->getWeekDay(1));
        $this->assertEquals('Sunday', $h->getWeekDay(7));
    }
}
