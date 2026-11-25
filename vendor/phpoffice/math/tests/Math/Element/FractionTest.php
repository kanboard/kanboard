<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Element;

use PhpOffice\Math\Element;
use PhpOffice\Math\Element\Fraction;
use PHPUnit\Framework\TestCase;

class FractionTest extends TestCase
{
    public function testConstruct(): void
    {
        $identifierA = new Element\Identifier('a');
        $identifierB = new Element\Identifier('b');

        $fraction = new Fraction($identifierA, $identifierB);

        $this->assertEquals($identifierA, $fraction->getNumerator());
        $this->assertEquals($identifierB, $fraction->getDenominator());
    }

    public function testBase(): void
    {
        $identifierA = new Element\Identifier('a');
        $identifierB = new Element\Identifier('b');
        $identifierC = new Element\Identifier('c');

        $fraction = new Fraction($identifierA, $identifierB);

        $this->assertEquals($identifierA, $fraction->getNumerator());
        $this->assertInstanceOf(Fraction::class, $fraction->setNumerator($identifierC));
        $this->assertEquals($identifierC, $fraction->getNumerator());
    }

    public function testFraction(): void
    {
        $identifierA = new Element\Identifier('a');
        $identifierB = new Element\Identifier('b');
        $identifierC = new Element\Identifier('c');

        $fraction = new Fraction($identifierA, $identifierB);

        $this->assertEquals($identifierB, $fraction->getDenominator());
        $this->assertInstanceOf(Fraction::class, $fraction->setDenominator($identifierC));
        $this->assertEquals($identifierC, $fraction->getDenominator());
    }
}
