<?php

require_once __DIR__.'/Base.php';

use Model\DateParser;

class DateParserTest extends Base
{
    public function testValidDate()
    {
        $d = new DateParser($this->registry);

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
        $d = new DateParser($this->registry);

        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getTimestamp('2014-03-05')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getTimestamp('2014_03_05')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $d->getTimestamp('03/05/2014')));
    }
}
