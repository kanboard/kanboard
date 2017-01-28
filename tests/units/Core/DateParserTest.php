<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\DateParser;

class DateParserTest extends Base
{
    public function testGetTimeFormats()
    {
        $dateParser = new DateParser($this->container);
        $this->assertCount(2, $dateParser->getTimeFormats());
        $this->assertContains('H:i', $dateParser->getTimeFormats());
        $this->assertContains('g:i a', $dateParser->getTimeFormats());
    }

    public function testGetDateFormats()
    {
        $dateParser = new DateParser($this->container);
        $this->assertCount(4, $dateParser->getDateFormats());
        $this->assertCount(6, $dateParser->getDateFormats(true));
        $this->assertContains('d/m/Y', $dateParser->getDateFormats());
        $this->assertNotContains('Y-m-d', $dateParser->getDateFormats());
        $this->assertContains('Y-m-d', $dateParser->getDateFormats(true));
    }

    public function testThatUserDateFormatIsReturnedFirst()
    {
        $dateParser = new DateParser($this->container);

        $dates = $dateParser->getDateFormats();
        $this->assertEquals('m/d/Y', $dates[0]);

        $dates = $dateParser->getDateFormats(true);
        $this->assertEquals('m/d/Y', $dates[0]);

        $this->container['configModel']->save(array('application_date_format' => 'd/m/Y'));
        $this->container['memoryCache']->flush();

        $dates = $dateParser->getDateFormats();
        $this->assertEquals('d/m/Y', $dates[0]);

        $dates = $dateParser->getDateFormats(true);
        $this->assertEquals('d/m/Y', $dates[0]);
    }

    public function testGetDateTimeFormats()
    {
        $dateParser = new DateParser($this->container);
        $this->assertCount(8, $dateParser->getDateTimeFormats());
        $this->assertCount(12, $dateParser->getDateTimeFormats(true));
        $this->assertContains('d/m/Y H:i', $dateParser->getDateTimeFormats());
        $this->assertNotContains('Y-m-d H:i', $dateParser->getDateTimeFormats());
        $this->assertContains('Y-m-d g:i a', $dateParser->getDateTimeFormats(true));
    }

    public function testThatUserDateTimeFormatIsReturnedFirst()
    {
        $dateParser = new DateParser($this->container);

        $dates = $dateParser->getDateTimeFormats();
        $this->assertEquals('m/d/Y H:i', $dates[0]);

        $dates = $dateParser->getDateTimeFormats(true);
        $this->assertEquals('m/d/Y H:i', $dates[0]);

        $this->container['configModel']->save(array('application_date_format' => 'd/m/Y', 'application_time_format' => 'g:i a'));
        $this->container['memoryCache']->flush();

        $dates = $dateParser->getDateTimeFormats();
        $this->assertEquals('d/m/Y g:i a', $dates[0]);

        $dates = $dateParser->getDateTimeFormats(true);
        $this->assertEquals('d/m/Y g:i a', $dates[0]);
    }

    public function testGetAllDateFormats()
    {
        $dateParser = new DateParser($this->container);
        $this->assertCount(12, $dateParser->getAllDateFormats());
        $this->assertCount(18, $dateParser->getAllDateFormats(true));
        $this->assertContains('d/m/Y', $dateParser->getAllDateFormats());
        $this->assertContains('d/m/Y H:i', $dateParser->getAllDateFormats());
        $this->assertNotContains('Y-m-d H:i', $dateParser->getAllDateFormats());
        $this->assertContains('Y-m-d g:i a', $dateParser->getAllDateFormats(true));
        $this->assertContains('Y-m-d', $dateParser->getAllDateFormats(true));
    }

    public function testGetAllAvailableFormats()
    {
        $dateParser = new DateParser($this->container);

        $formats = $dateParser->getAvailableFormats($dateParser->getDateFormats());
        $this->assertArrayHasKey('d/m/Y', $formats);
        $this->assertContains(date('d/m/Y').' (d/m/Y)', $formats);

        $formats = $dateParser->getAvailableFormats($dateParser->getDateTimeFormats());
        $this->assertArrayHasKey('d/m/Y H:i', $formats);
        $this->assertContains(date('d/m/Y H:i').' (d/m/Y H:i)', $formats);

        $formats = $dateParser->getAvailableFormats($dateParser->getAllDateFormats());
        $this->assertArrayHasKey('d/m/Y', $formats);
        $this->assertContains(date('d/m/Y').' (d/m/Y)', $formats);
        $this->assertArrayHasKey('d/m/Y H:i', $formats);
        $this->assertContains(date('d/m/Y H:i').' (d/m/Y H:i)', $formats);
    }

    public function testGetTimestampFromDefaultFormats()
    {
        $dateParser = new DateParser($this->container);

        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('06/09/2016')));
        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('2016-06-09')));
        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('2016_06_09')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('2016-06-09 21:15')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('2016_06_09 21:15')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('06/09/2016 21:15')));
    }

    public function testGetTimestampFromUserDateFormats()
    {
        $this->container['configModel']->save(array(
            'application_date_format' => 'd/m/Y',
            'application_time_format' => 'g:i a',
        ));

        $dateParser = new DateParser($this->container);

        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('09/06/2016')));
        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('2016-06-09')));
        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('2016_06_09')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('2016-06-09 21:15')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('2016_06_09 21:15')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('09/06/2016 9:15 pm')));
    }

    public function testGetTimestampFromAnotherUserDateFormats()
    {
        $this->container['configModel']->save(array(
            'application_date_format' => 'd.m.Y',
            'application_time_format' => 'H:i',
        ));

        $dateParser = new DateParser($this->container);

        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('09.06.2016')));
        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('2016-06-09')));
        $this->assertEquals('2016-06-09', date('Y-m-d', $dateParser->getTimestamp('2016_06_09')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('2016-06-09 21:15')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('2016_06_09 21:15')));
        $this->assertEquals('2016-06-09 21:15', date('Y-m-d H:i', $dateParser->getTimestamp('09.06.2016 21:15')));
    }

    public function testDateRange()
    {
        $dateParser = new DateParser($this->container);

        $this->assertTrue($dateParser->withinDateRange(new DateTime('2015-03-14 15:30:00'), new DateTime('2015-03-14 15:00:00'), new DateTime('2015-03-14 16:00:00')));
        $this->assertFalse($dateParser->withinDateRange(new DateTime('2015-03-14 15:30:00'), new DateTime('2015-03-14 16:00:00'), new DateTime('2015-03-14 17:00:00')));
    }

    public function testGetHours()
    {
        $dateParser = new DateParser($this->container);

        $this->assertEquals(1, $dateParser->getHours(new DateTime('2015-03-14 15:00:00'), new DateTime('2015-03-14 16:00:00')));
        $this->assertEquals(2.5, $dateParser->getHours(new DateTime('2015-03-14 15:00:00'), new DateTime('2015-03-14 17:30:00')));
        $this->assertEquals(2.75, $dateParser->getHours(new DateTime('2015-03-14 15:00:00'), new DateTime('2015-03-14 17:45:00')));
        $this->assertEquals(3.02, $dateParser->getHours(new DateTime('2015-03-14 14:57:00'), new DateTime('2015-03-14 17:58:00')));
        $this->assertEquals(2.98, $dateParser->getHours(new DateTime('2015-03-14 14:57:00'), new DateTime('2015-03-14 11:58:00')));
        $this->assertEquals(0, $dateParser->getHours(new DateTime('2015-03-14 14:57:00'), new DateTime('2015-03-14 14:57:10')));
    }

    public function testGetIsoDate()
    {
        $dateParser = new DateParser($this->container);

        $this->assertEquals('2016-02-06', $dateParser->getIsoDate(1454786217));
        $this->assertEquals('2014-03-05', $dateParser->getIsoDate('2014-03-05'));
        $this->assertEquals('2014-03-05', $dateParser->getIsoDate('2014_03_05'));
        $this->assertEquals('2014-03-05', $dateParser->getIsoDate('03/05/2014'));
        $this->assertEquals('2014-03-25', $dateParser->getIsoDate('03/25/2014 23:14'));
        $this->assertEquals('2014-03-29', $dateParser->getIsoDate('2014_03_29 23:14'));
        $this->assertEquals('2014-03-29', $dateParser->getIsoDate('2014-03-29 23:14'));
    }

    public function testGetTimestampFromIsoFormat()
    {
        $dateParser = new DateParser($this->container);
        $this->assertEquals('2014-03-05 00:00', date('Y-m-d H:i', $dateParser->getTimestampFromIsoFormat('2014-03-05')));
        $this->assertEquals(date('Y-m-d 00:00', strtotime('+2 days')), date('Y-m-d H:i', $dateParser->getTimestampFromIsoFormat(strtotime('+2 days'))));
    }

    public function testRemoveTimeFromTimestamp()
    {
        $dateParser = new DateParser($this->container);
        $this->assertEquals('2016-02-06 00:00', date('Y-m-d H:i', $dateParser->removeTimeFromTimestamp(1454786217)));
    }

    public function testFormat()
    {
        $dateParser = new DateParser($this->container);
        $values['date'] = '1454787006';

        $this->assertEquals(array('date' => '06/02/2016'), $dateParser->format($values, array('date'), 'd/m/Y'));
        $this->assertEquals(array('date' => '02/06/2016 7:30 pm'), $dateParser->format($values, array('date'), 'm/d/Y g:i a'));
    }

    public function testConvert()
    {
        $dateParser = new DateParser($this->container);
        $values = array(
            'date_due' => '2015-01-25',
            'date_started' => '2015-01-25 17:25',
        );

        $this->assertEquals(
            array('date_due' => 1422144000, 'date_started' => 1422144000),
            $dateParser->convert($values, array('date_due', 'date_started'))
        );

        $values = array(
            'date_started' => '2015-01-25 17:25',
        );

        $this->assertEquals(
            array('date_started' => 1422206700),
            $dateParser->convert($values, array('date_due', 'date_started'), true)
        );
    }
}
