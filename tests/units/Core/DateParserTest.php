<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\DateParser;

class DateParserTest extends Base
{
    public function testDateRange()
    {
        $d = new DateParser($this->container);

        $this->assertTrue($d->withinDateRange(new DateTime('2015-03-14 15:30:00'), new DateTime('2015-03-14 15:00:00'), new DateTime('2015-03-14 16:00:00')));
        $this->assertFalse($d->withinDateRange(new DateTime('2015-03-14 15:30:00'), new DateTime('2015-03-14 16:00:00'), new DateTime('2015-03-14 17:00:00')));
    }

    public function testRoundSeconds()
    {
        $d = new DateParser($this->container);
        $this->assertEquals('16:30', date('H:i', $d->getRoundedSeconds(strtotime('16:28'))));
        $this->assertEquals('16:00', date('H:i', $d->getRoundedSeconds(strtotime('16:02'))));
        $this->assertEquals('16:15', date('H:i', $d->getRoundedSeconds(strtotime('16:14'))));
        $this->assertEquals('17:00', date('H:i', $d->getRoundedSeconds(strtotime('16:58'))));
    }

    public function testGetHours()
    {
        $d = new DateParser($this->container);

        $this->assertEquals(1, $d->getHours(new DateTime('2015-03-14 15:00:00'), new DateTime('2015-03-14 16:00:00')));
        $this->assertEquals(2.5, $d->getHours(new DateTime('2015-03-14 15:00:00'), new DateTime('2015-03-14 17:30:00')));
        $this->assertEquals(2.75, $d->getHours(new DateTime('2015-03-14 15:00:00'), new DateTime('2015-03-14 17:45:00')));
        $this->assertEquals(3, $d->getHours(new DateTime('2015-03-14 14:57:00'), new DateTime('2015-03-14 17:58:00')));
        $this->assertEquals(3, $d->getHours(new DateTime('2015-03-14 14:57:00'), new DateTime('2015-03-14 11:58:00')));
    }

    public function testValidDate()
    {
        $d = new DateParser($this->container);

        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getValidDate('2014-03-05', 'Y-m-d')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getValidDate('2014_03_05', 'Y_m_d')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getValidDate('05/03/2014', 'd/m/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getValidDate('03/05/2014', 'm/d/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getValidDate('3/5/2014', 'm/d/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getValidDate('5/3/2014', 'd/m/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getValidDate('5/3/14', 'd/m/y')));
        $this->assertEquals(0, $d->getValidDate('5/3/14', 'd/m/Y'));
        $this->assertEquals(0, $d->getValidDate('5-3-2014', 'd/m/Y'));
    }

    public function testGetTimestamp()
    {
        $d = new DateParser($this->container);

        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getTimestamp('2014-03-05')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getTimestamp('2014_03_05')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getTimestamp('03/05/2014')));
        $this->assertEquals('2014-03-25 17:18', date('Y-m-d H:i', $d->getTimestamp('03/25/2014 5:18 pm')));
        $this->assertEquals('2014-03-25 05:18', date('Y-m-d H:i', $d->getTimestamp('03/25/2014 5:18 am')));
        $this->assertEquals('2014-03-25 05:18', date('Y-m-d H:i', $d->getTimestamp('03/25/2014 5:18am')));
        $this->assertEquals('2014-03-25 23:14', date('Y-m-d H:i', $d->getTimestamp('03/25/2014 23:14')));
        $this->assertEquals('2014-03-29 23:14', date('Y-m-d H:i', $d->getTimestamp('2014_03_29 23:14')));
        $this->assertEquals('2014-03-29 23:14', date('Y-m-d H:i', $d->getTimestamp('2014-03-29 23:14')));
    }

    public function testConvert()
    {
        $d = new DateParser($this->container);

        $values = array(
            'date_due' => '2015-01-25',
            'date_started' => '2015_01_25',
        );

        $d->convert($values, array('date_due', 'date_started'));

        $this->assertEquals(mktime(0, 0, 0, 1, 25, 2015), $values['date_due']);
        $this->assertEquals('2015-01-25', date('Y-m-d', $values['date_due']));

        $this->assertEquals(mktime(0, 0, 0, 1, 25, 2015), $values['date_started']);
        $this->assertEquals('2015-01-25', date('Y-m-d', $values['date_started']));
    }
}
