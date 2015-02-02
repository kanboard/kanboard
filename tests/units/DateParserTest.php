<?php

require_once __DIR__.'/Base.php';

use Model\DateParser;

class DateParserTest extends Base
{
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
