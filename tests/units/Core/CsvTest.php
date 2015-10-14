<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Csv;

class CsvTest extends Base
{
    public function testGetBooleanValue()
    {
        $this->assertEquals(1, Csv::getBooleanValue('1'));
        $this->assertEquals(1, Csv::getBooleanValue('True'));
        $this->assertEquals(1, Csv::getBooleanValue('t'));
        $this->assertEquals(1, Csv::getBooleanValue('TRUE'));
        $this->assertEquals(1, Csv::getBooleanValue('true'));
        $this->assertEquals(1, Csv::getBooleanValue('T'));

        $this->assertEquals(0, Csv::getBooleanValue('0'));
        $this->assertEquals(0, Csv::getBooleanValue('123'));
        $this->assertEquals(0, Csv::getBooleanValue('anything'));
    }
}
