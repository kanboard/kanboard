<?php

namespace Eluceo\iCal;

class PropertyTest extends \PHPUnit_Framework_TestCase
{
    public function testPropertyWithSingleValue()
    {
        $property = new Property('DTSTAMP', '20131020T153112');
        $this->assertEquals(
            'DTSTAMP:20131020T153112',
            $property->toLine()
        );
    }

    public function testPropertyWithValueAndParams()
    {
        $property = new Property('DTSTAMP', '20131020T153112', array('TZID' => 'Europe/Berlin'));
        $this->assertEquals(
            'DTSTAMP;TZID=Europe/Berlin:20131020T153112',
            $property->toLine()
        );
    }

    public function testPropertyWithEscapedSingleValue()
    {
        $property = new Property('SOMEPROP', 'Escape me!"');
        $this->assertEquals(
            'SOMEPROP:Escape me!\\"',
            $property->toLine()
        );
    }

    public function testPropertyWithEscapedValues()
    {
        $property = new Property('SOMEPROP', 'Escape me!"', array('TEST' => 'Lorem "test" ipsum'));
        $this->assertEquals(
            'SOMEPROP;TEST="Lorem \\"test\\" ipsum":Escape me!\\"',
            $property->toLine()
        );
    }
}
