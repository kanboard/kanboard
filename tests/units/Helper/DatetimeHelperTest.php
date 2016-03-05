<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\DateHelper;

class DatetimeHelperTest extends Base
{
    public function testGetTime()
    {
        $helper = new DateHelper($this->container);
        $this->assertEquals('17:25', $helper->time(1422206700));
    }

    public function testGetDate()
    {
        $helper = new DateHelper($this->container);
        $this->assertEquals('01/25/2015', $helper->date(1422206700));
        $this->assertEquals('01/25/2015', $helper->date('2015-01-25'));
        $this->assertEquals('', $helper->date('0'));
        $this->assertEquals('', $helper->date(0));
        $this->assertEquals('', $helper->date(''));
    }

    public function testGetDatetime()
    {
        $helper = new DateHelper($this->container);
        $this->assertEquals('01/25/2015 17:25', $helper->datetime(1422206700));
    }

    public function testAge()
    {
        $helper = new DateHelper($this->container);

        $this->assertEquals('&lt;15m', $helper->age(0, 30));
        $this->assertEquals('&lt;30m', $helper->age(0, 1000));
        $this->assertEquals('&lt;1h', $helper->age(0, 3000));
        $this->assertEquals('~2h', $helper->age(0, 2*3600));
        $this->assertEquals('1d', $helper->age(0, 30*3600));
        $this->assertEquals('2d', $helper->age(0, 65*3600));
    }

    public function testGetDayHours()
    {
        $helper = new DateHelper($this->container);

        $slots = $helper->getDayHours();

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
        $helper = new DateHelper($this->container);

        $slots = $helper->getWeekDays();

        $this->assertNotEmpty($slots);
        $this->assertCount(7, $slots);
        $this->assertContains('Monday', $slots);
        $this->assertContains('Sunday', $slots);
    }

    public function testGetWeekDay()
    {
        $helper = new DateHelper($this->container);

        $this->assertEquals('Monday', $helper->getWeekDay(1));
        $this->assertEquals('Sunday', $helper->getWeekDay(7));
    }
}
