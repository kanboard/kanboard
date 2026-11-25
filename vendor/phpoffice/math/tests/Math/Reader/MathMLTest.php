<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Reader;

use PhpOffice\Math\Element;
use PhpOffice\Math\Exception\InvalidInputException;
use PhpOffice\Math\Exception\NotImplementedException;
use PhpOffice\Math\Exception\SecurityException;
use PhpOffice\Math\Math;
use PhpOffice\Math\Reader\MathML;
use PHPUnit\Framework\TestCase;

class MathMLTest extends TestCase
{
    public function testReadBasic(): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">
        <math xmlns="http://www.w3.org/1998/Math/MathML">
            <mrow>
                <mi>a</mi> <mo>&InvisibleTimes;</mo> <msup><mi>x</mi><mn>2</mn></msup>
                <mo>+</mo><mi>b</mi><mo>&InvisibleTimes;</mo><mi>x</mi>
                <mo>+</mo><mi>c</mi>
            </mrow>
        </math>';

        $reader = new MathML();
        $math = $reader->read($content);
        $this->assertInstanceOf(Math::class, $math);

        $elements = $math->getElements();
        $this->assertCount(1, $elements);
        $this->assertInstanceOf(Element\Row::class, $elements[0]);

        /** @var Element\Row $element */
        $element = $elements[0];
        $subElements = $element->getElements();
        $this->assertCount(9, $subElements);

        /** @var Element\Identifier $subElement */
        $subElement = $subElements[0];
        $this->assertInstanceOf(Element\Identifier::class, $subElement);
        $this->assertEquals('a', $subElement->getValue());

        /** @var Element\Identifier $subElement */
        $subElement = $subElements[1];
        $this->assertInstanceOf(Element\Operator::class, $subElement);
        $this->assertEquals('InvisibleTimes', $subElement->getValue());

        /** @var Element\Superscript $subElement */
        $subElement = $subElements[2];
        $this->assertInstanceOf(Element\Superscript::class, $subElements[2]);

        /** @var Element\Identifier $base */
        $base = $subElement->getBase();
        $this->assertInstanceOf(Element\Identifier::class, $base);
        $this->assertEquals('x', $base->getValue());

        /** @var Element\Numeric $superscript */
        $superscript = $subElement->getSuperscript();
        $this->assertInstanceOf(Element\Numeric::class, $superscript);
        $this->assertEquals(2, $superscript->getValue());

        /** @var Element\Operator $subElement */
        $subElement = $subElements[3];
        $this->assertInstanceOf(Element\Operator::class, $subElement);
        $this->assertEquals('+', $subElement->getValue());

        /** @var Element\Identifier $subElement */
        $subElement = $subElements[4];
        $this->assertInstanceOf(Element\Identifier::class, $subElement);
        $this->assertEquals('b', $subElement->getValue());

        /** @var Element\Operator $subElement */
        $subElement = $subElements[5];
        $this->assertInstanceOf(Element\Operator::class, $subElement);
        $this->assertEquals('InvisibleTimes', $subElement->getValue());

        /** @var Element\Identifier $subElement */
        $subElement = $subElements[6];
        $this->assertInstanceOf(Element\Identifier::class, $subElement);
        $this->assertEquals('x', $subElement->getValue());

        /** @var Element\Operator $subElement */
        $subElement = $subElements[7];
        $this->assertInstanceOf(Element\Operator::class, $subElement);
        $this->assertEquals('+', $subElement->getValue());

        /** @var Element\Identifier $subElement */
        $subElement = $subElements[8];
        $this->assertInstanceOf(Element\Identifier::class, $subElement);
        $this->assertEquals('c', $subElement->getValue());
    }

    public function testReadFraction(): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">
        <math xmlns="http://www.w3.org/1998/Math/MathML">
            <mfrac bevelled="true">
                <mfrac>
                    <mi> a </mi>
                    <mi> b </mi>
                </mfrac>
                <mfrac>
                    <mi> c </mi>
                    <mi> d </mi>
                </mfrac>
            </mfrac>
        </math>';

        $reader = new MathML();
        $math = $reader->read($content);
        $this->assertInstanceOf(Math::class, $math);

        $elements = $math->getElements();
        $this->assertCount(1, $elements);
        $this->assertInstanceOf(Element\Fraction::class, $elements[0]);

        /** @var Element\Fraction $element */
        $element = $elements[0];

        $this->assertInstanceOf(Element\Fraction::class, $element->getNumerator());
        /** @var Element\Fraction $subElement */
        $subElement = $element->getNumerator();

        /** @var Element\Identifier $numerator */
        $numerator = $subElement->getNumerator();
        $this->assertInstanceOf(Element\Identifier::class, $numerator);
        $this->assertEquals('a', $numerator->getValue());
        /** @var Element\Identifier $denominator */
        $denominator = $subElement->getDenominator();
        $this->assertInstanceOf(Element\Identifier::class, $denominator);
        $this->assertEquals('b', $denominator->getValue());

        $this->assertInstanceOf(Element\Fraction::class, $element->getDenominator());
        /** @var Element\Fraction $subElement */
        $subElement = $element->getDenominator();

        /** @var Element\Identifier $numerator */
        $numerator = $subElement->getNumerator();
        $this->assertInstanceOf(Element\Identifier::class, $numerator);
        $this->assertEquals('c', $numerator->getValue());
        /** @var Element\Identifier $denominator */
        $denominator = $subElement->getDenominator();
        $this->assertInstanceOf(Element\Identifier::class, $denominator);
        $this->assertEquals('d', $denominator->getValue());
    }

    public function testReadFractionInvalid(): void
    {
        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('PhpOffice\Math\Reader\MathML::getElement : The tag `mfrac` has not two subelements');

        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">
        <math xmlns="http://www.w3.org/1998/Math/MathML">
            <mfrac>
                <mi> a </mi>
            </mfrac>
        </math>';

        $reader = new MathML();
        $math = $reader->read($content);
    }

    public function testReadFractionWithRow(): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">
        <math xmlns="http://www.w3.org/1998/Math/MathML">
            <mfrac>
                <mrow>
                    <mn>3</mn>
                    <mo>-</mo>
                    <mi>x</mi>
                </mrow>
                <mn>2</mn>
            </mfrac>
        </math>';

        $reader = new MathML();
        $math = $reader->read($content);
        $this->assertInstanceOf(Math::class, $math);

        $elements = $math->getElements();
        $this->assertCount(1, $elements);
        $this->assertInstanceOf(Element\Fraction::class, $elements[0]);

        /** @var Element\Fraction $element */
        $element = $elements[0];

        $this->assertInstanceOf(Element\Row::class, $element->getNumerator());
        /** @var Element\Row $subElement */
        $subElement = $element->getNumerator();

        $subsubElements = $subElement->getElements();
        $this->assertCount(3, $subsubElements);

        /** @var Element\Numeric $subsubElement */
        $subsubElement = $subsubElements[0];
        $this->assertInstanceOf(Element\Numeric::class, $subsubElement);
        $this->assertEquals('3', $subsubElement->getValue());

        /** @var Element\Operator $subsubElement */
        $subsubElement = $subsubElements[1];
        $this->assertInstanceOf(Element\Operator::class, $subsubElement);
        $this->assertEquals('-', $subsubElement->getValue());

        /** @var Element\Identifier $subsubElement */
        $subsubElement = $subsubElements[2];
        $this->assertInstanceOf(Element\Identifier::class, $subsubElement);
        $this->assertEquals('x', $subsubElement->getValue());

        $this->assertInstanceOf(Element\Numeric::class, $element->getDenominator());
        /** @var Element\Numeric $subElement */
        $subElement = $element->getDenominator();
        $this->assertEquals('2', $subElement->getValue());
    }

    public function testReadSuperscriptInvalid(): void
    {
        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('PhpOffice\Math\Reader\MathML::getElement : The tag `msup` has not two subelements');

        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">
        <math xmlns="http://www.w3.org/1998/Math/MathML">
            <msup>
                <mi> a </mi>
            </msup>
        </math>';

        $reader = new MathML();
        $math = $reader->read($content);
    }

    public function testReadSemantics(): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <math xmlns="http://www.w3.org/1998/Math/MathML" display="block">
            <semantics>
                <mrow>
                    <mfrac>
                        <mi>π</mi>
                        <mn>2</mn>
                    </mfrac>
                    <mo stretchy="false">+</mo>
                    <mrow>
                        <mi>a</mi>
                        <mo stretchy="false">∗</mo>
                        <mn>2</mn>
                    </mrow>
                </mrow>
                <annotation encoding="StarMath 5.0">{π} over {2}  + { a } * 2 </annotation>
            </semantics>
        </math>';

        $reader = new MathML();
        $math = $reader->read($content);
        $this->assertInstanceOf(Math::class, $math);

        $elements = $math->getElements();
        $this->assertCount(1, $elements);
        $this->assertInstanceOf(Element\Semantics::class, $elements[0]);

        /** @var Element\Semantics $element */
        $element = $elements[0];

        // Check MathML
        $subElements = $element->getElements();
        $this->assertCount(1, $subElements);
        $this->assertInstanceOf(Element\Row::class, $subElements[0]);

        // Check Annotation
        $this->assertCount(1, $element->getAnnotations());
        $this->assertEquals('{π} over {2}  + { a } * 2', $element->getAnnotation('StarMath 5.0'));
    }

    public function testReadNotImplemented(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('PhpOffice\Math\Reader\MathML::getElement : The tag `mnotexisting` is not implemented');

        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">
        <math xmlns="http://www.w3.org/1998/Math/MathML">
            <mnotexisting>
                <mi> a </mi>
            </mnotexisting>
        </math>';

        $reader = new MathML();
        $math = $reader->read($content);
    }

    public function testReadSecurity(): void
    {
        $this->expectException(SecurityException::class);
        $this->expectExceptionMessage('Detected use of ENTITY in XML, loading aborted to prevent XXE/XEE attacks');

        $content = '<?xml version="1.0" encoding="UTF-8"?>     <!DOCTYPE x SYSTEM "php://filter/convert.base64-decode/zlib.inflate/resource=data:,7Ztdb9owFIbv%2bRVZJ9armNjOZ2k7QUaL%2bRYO2nqFUnBFNQaMptP272cnNFuTsBbSskg1iATZzvGxn/ccX3A4fdfoecS7UsrK1A98hV5Rr9FVjlaz1UmlcnM7D9i6MlkufrB1AK79O2bqKltMllMWt96KL6ADwci7sJ4Yu0vr9/tlwKbqan27CPzrOXvevFGrbRvOGIseaCa7TAxok1x44xahXzQEcdKPKZPevap3RZw920I0VscWGLlU1efPsy0c5cbV1AoI7ZuOMCZW12nkcP9Q2%2bQObBNmL6ajg8s6xJqmJTrq5NIArX6zVk8Zcwwt4fPuLvHnbeBSvpdIQ6g93MvUv3CHqKNrmtEW4EYmCr5gDT5QzyNWE4x6xO1/aqQmgMhGYgaVDFUnScKltbFnaJoKHRuHK0L1pIkuaYselMe9cPUqRmm5C51u00kkhy1S3aBougkl7e4d6RGaTYeSehdCjAG/O/p%2bYfKyQsoLmgdlmsFYQFDjh6GWJyGE0ZfMX08EZtwNTdAYud7nLcksnwppA2UnqpCzgyDo1QadAU3vLOQZ82EHMxAi0KVcq7rzas5xD6AQoeqkYkgk02abukkJ/z%2bNvkj%2bjUy16Ba5d/S8anhBLwt44EgGkoFkIBlIBpKBZCAZSAaSgWQgGUgGkoFkIBlIBpKBZCAZSAaSgWQgGUgGxWOwW2nF7kt%2by7/Kb3ag2GUTUgBvXAAxiKxt4Is3sB4WniVrOvhwzB0CXerg5GN9esGRQv7RgQdMmMO9sIwtc/sIJUOCsY4ee7f7FIWu2Si4euKan8wg58nFsEIXxYGntgZqMog3Z2FrgPhgyzIOlsmijowqwb0jyMqMoGEbarqdOpP/iqFISMkSVFG1Z5p8f3OK%2bxAZ7gClpgUPg70rq0T2RIkcup/0newQ7NbcUXv/DPl4LL/N7hdfn2dp07pmd8v79YSdVVgwqcyWd8HC/8aOzkunf6r%2b2c8bpSxK/6uPmlf%2br/nSnyrHcduH99iqKiz7HwLxTLMgEM0QWUDjb3ji8NdHPslZmV%2bqR%2bfH56Xyxni1VGbV0m8=" []><foo></foo>M';

        $reader = new MathML();
        $math = $reader->read($content);
    }
}
