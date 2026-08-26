<?php

namespace KanboardTests\units\Core;

use KanboardTests\units\Base;
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
        $this->assertEquals(1, Csv::getBooleanValue('Y'));
        $this->assertEquals(1, Csv::getBooleanValue('y'));
        $this->assertEquals(1, Csv::getBooleanValue('yes'));
        $this->assertEquals(1, Csv::getBooleanValue('Yes'));

        $this->assertEquals(0, Csv::getBooleanValue('0'));
        $this->assertEquals(0, Csv::getBooleanValue('123'));
        $this->assertEquals(0, Csv::getBooleanValue('anything'));
    }

    public function testGetEnclosures()
    {
        $this->assertCount(3, Csv::getEnclosures());
        $this->assertCount(4, Csv::getDelimiters());
    }

    public function testReadWrite()
    {
        $filename = tempnam(sys_get_temp_dir(), 'UT');
        $rows = array(
            array('Column A', 'Column B'),
            array('value a', 'value b'),
        );

        $csv = new Csv;
        $csv->write($filename, $rows);
        $csv->setColumnMapping(array('A', 'B', 'C'));
        $csv->read($filename, array($this, 'readRow'));

        unlink($filename);

        $this->expectOutputString('"Column A","Column B"'."\n".'"value a","value b"'."\n", $csv->output($rows));
    }

    public function testWriteNeutralizesFormulaInjection()
    {
        $filename = tempnam(sys_get_temp_dir(), 'UT');
        $rows = array(array(
            '=1+1',
            '+1+1',
            '-1+1',
            '@SUM(A1:A2)',
            "\t=1+1",
            "\r=1+1",
            "\n=1+1",
        ));

        $csv = new Csv;
        $csv->write($filename, $rows);

        $fp = fopen($filename, 'r');
        $row = fgetcsv($fp, null, ',', '"', '\\');
        fclose($fp);
        unlink($filename);

        $this->assertSame(array(
            "'=1+1",
            "'+1+1",
            "'-1+1",
            "'@SUM(A1:A2)",
            "'\t=1+1",
            "'\r=1+1",
            "'\n=1+1",
        ), $row);
    }

    public function testWritePreservesSafeValues()
    {
        $filename = tempnam(sys_get_temp_dir(), 'UT');
        $rows = array(array(
            'Text',
            '1=1',
            '',
            123,
            -123,
            1.5,
            null,
            '-2',
            '+2',
            '-1.5',
            '-2e3',
        ));

        $csv = new Csv;
        $csv->write($filename, $rows);

        $fp = fopen($filename, 'r');
        $row = fgetcsv($fp, null, ',', '"', '\\');
        fclose($fp);
        unlink($filename);

        $this->assertSame(
            array('Text', '1=1', '', '123', '-123', '1.5', '', '-2', '+2', '-1.5', '-2e3'),
            $row
        );
    }

    public function readRow(array $row, $line)
    {
        $this->assertEquals(array('value a', 'value b', ''), $row);
        $this->assertEquals(1, $line);
    }
}
