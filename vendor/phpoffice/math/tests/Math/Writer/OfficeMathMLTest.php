<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Writer;

use PhpOffice\Math\Element;
use PhpOffice\Math\Exception\NotImplementedException;
use PhpOffice\Math\Math;
use PhpOffice\Math\Writer\OfficeMathML;

class OfficeMathMLTest extends WriterTestCase
{
    public function testWriteFraction(): void
    {
        $math = new Math();

        $fraction = new Element\Fraction(
            new Element\Identifier('π'),
            new Element\Numeric(2)
        );
        $math->add($fraction);

        $writer = new OfficeMathML();
        $output = $writer->write($math);

        $expected = '<m:oMathPara xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math">'
            . '<m:oMath>'
            . '<m:f>'
            . '<m:num><m:r><m:t>π</m:t></m:r></m:num>'
            . '<m:den><m:r><m:t>2</m:t></m:r></m:den>'
            . '</m:f>'
            . '</m:oMath>'
            . '</m:oMathPara>';
        $this->assertEquals($expected, $output);
    }

    public function testWriteRow(): void
    {
        $math = new Math();

        $row = new Element\Row();
        $math->add($row);

        $row->add(new Element\Identifier('x'));

        $writer = new OfficeMathML();
        $output = $writer->write($math);

        $expected = '<m:oMathPara xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math">'
            . '<m:oMath>'
            . '<m:r><m:t>x</m:t></m:r>'
            . '</m:oMath>'
            . '</m:oMathPara>';
        $this->assertEquals($expected, $output);
    }

    public function testWriteNotImplemented(): void
    {
        $this->expectException(NotImplementedException::class);
        if (method_exists($this, 'expectExceptionMessageRegExp')) {
            $this->expectExceptionMessageRegExp('/PhpOffice\\\Math\\\Writer\\\OfficeMathML::getElementTagName : The element of the class/');
            $this->expectExceptionMessageRegExp('/has no tag name/');
        } else {
            // @phpstan-ignore-next-line
            $this->expectExceptionMessageMatches('/PhpOffice\\\Math\\\Writer\\\OfficeMathML::getElementTagName : The element of the class/');
            // @phpstan-ignore-next-line
            $this->expectExceptionMessageMatches('/has no tag name/');
        }

        $math = new Math();

        $object = new class extends Element\AbstractElement {};
        $math->add($object);

        $writer = new OfficeMathML();
        $output = $writer->write($math);
    }
}
