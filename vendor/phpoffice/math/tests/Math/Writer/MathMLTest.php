<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Writer;

use PhpOffice\Math\Element;
use PhpOffice\Math\Exception\NotImplementedException;
use PhpOffice\Math\Math;
use PhpOffice\Math\Writer\MathML;

class MathMLTest extends WriterTestCase
{
    public function testWrite(): void
    {
        $opTimes = new Element\Operator('&InvisibleTimes;');

        $math = new Math();

        $row = new Element\Row();
        $math->add($row);

        $row->add(new Element\Identifier('a'));
        $row->add(clone $opTimes);

        $superscript = new Element\Superscript(
            new Element\Identifier('x'),
            new Element\Numeric(2)
        );
        $row->add($superscript);

        $row->add(new Element\Operator('+'));

        $row->add(new Element\Identifier('b'));
        $row->add(clone $opTimes);
        $row->add(new Element\Identifier('x'));

        $row->add(new Element\Operator('+'));

        $row->add(new Element\Identifier('c'));

        $writer = new MathML();
        $output = $writer->write($math);

        $expected = '<?xml version="1.0" encoding="UTF-8"?>'
            . PHP_EOL
            . '<!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">'
            . '<math xmlns="http://www.w3.org/1998/Math/MathML">'
            . '<mrow><mi>a</mi><mo>&amp;InvisibleTimes;</mo><msup><mi>x</mi><mn>2</mn></msup><mo>+</mo><mi>b</mi><mo>&amp;InvisibleTimes;</mo><mi>x</mi><mo>+</mo><mi>c</mi>'
            . '</mrow>'
            . '</math>'
            . PHP_EOL;
        $this->assertEquals($expected, $output);
        $this->assertIsSchemaMathMLValid($output);
    }

    public function testWriteFraction(): void
    {
        $math = new Math();

        $fraction = new Element\Fraction(
            new Element\Identifier('π'),
            new Element\Numeric(2)
        );
        $math->add($fraction);

        $writer = new MathML();
        $output = $writer->write($math);

        $expected = '<?xml version="1.0" encoding="UTF-8"?>'
            . PHP_EOL
            . '<!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">'
            . '<math xmlns="http://www.w3.org/1998/Math/MathML">'
            . '<mfrac>'
            . '<mi>π</mi><mn>2</mn>'
            . '</mfrac>'
            . '</math>'
            . PHP_EOL;
        $this->assertEquals($expected, $output);
        $this->assertIsSchemaMathMLValid($output);
    }

    public function testWriteSemantics(): void
    {
        $opTimes = new Element\Operator('&InvisibleTimes;');

        $math = new Math();

        $semantics = new Element\Semantics();
        $semantics->add(new Element\Identifier('y'));
        $semantics->addAnnotation('application/x-tex', ' y ');

        $math->add($semantics);

        $writer = new MathML();
        $output = $writer->write($math);

        $expected = '<?xml version="1.0" encoding="UTF-8"?>'
            . PHP_EOL
            . '<!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">'
            . '<math xmlns="http://www.w3.org/1998/Math/MathML">'
            . '<semantics>'
            . '<mi>y</mi>'
            . '<annotation encoding="application/x-tex"> y </annotation>'
            . '</semantics>'
            . '</math>'
            . PHP_EOL;
        $this->assertEquals($expected, $output);
        $this->assertIsSchemaMathMLValid($output);
    }

    public function testWriteNotImplemented(): void
    {
        $this->expectException(NotImplementedException::class);
        if (method_exists($this, 'expectExceptionMessageRegExp')) {
            $this->expectExceptionMessageRegExp('/PhpOffice\\\Math\\\Writer\\\MathML::getElementTagName : The element of the class/');
            $this->expectExceptionMessageRegExp('/has no tag name/');
        } else {
            // @phpstan-ignore-next-line
            $this->expectExceptionMessageMatches('/PhpOffice\\\Math\\\Writer\\\MathML::getElementTagName : The element of the class/');
            // @phpstan-ignore-next-line
            $this->expectExceptionMessageMatches('/has no tag name/');
        }

        $math = new Math();

        $object = new class extends Element\AbstractElement {};
        $math->add($object);

        $writer = new MathML();
        $output = $writer->write($math);
    }
}
