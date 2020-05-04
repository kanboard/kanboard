<?php

namespace Eluceo\iCal;

class ParameterBagTest extends \PHPUnit_Framework_TestCase
{
    public function testEscapeParamValue()
    {
        $propertyObject = new ParameterBag;

        $this->assertEquals(
            'test string',
            $propertyObject->escapeParamValue('test string'),
            'No escaping necessary'
        );

        $this->assertEquals(
            '"Containing \\"double-quotes\\""',
            $propertyObject->escapeParamValue('Containing "double-quotes"'),
            'Text contains double quotes'
        );

        $this->assertEquals(
            '"Containing forbidden chars like a ;"',
            $propertyObject->escapeParamValue('Containing forbidden chars like a ;'),
            'Text with semicolon'
        );

        $this->assertEquals(
            '"Containing forbidden chars like a :"',
            $propertyObject->escapeParamValue('Containing forbidden chars like a :'),
            'Text with colon'
        );
    }
}
