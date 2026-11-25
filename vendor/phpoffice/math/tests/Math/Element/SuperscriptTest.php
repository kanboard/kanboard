<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Element;

use PhpOffice\Math\Element;
use PhpOffice\Math\Element\Superscript;
use PHPUnit\Framework\TestCase;

class SuperscriptTest extends TestCase
{
    public function testConstruct(): void
    {
        $superscript = new Superscript(new Element\Identifier('a'), new Element\Identifier('a'));

        $this->assertInstanceOf(Element\Identifier::class, $superscript->getBase());
        $this->assertInstanceOf(Element\Identifier::class, $superscript->getSuperscript());
    }

    public function testBase(): void
    {
        $identifierA = new Element\Identifier('a');
        $identifierB = new Element\Identifier('b');
        $identifierC = new Element\Identifier('c');

        $superscript = new Superscript($identifierA, $identifierB);

        $this->assertEquals($identifierA, $superscript->getBase());
        $this->assertInstanceOf(Superscript::class, $superscript->setBase($identifierC));
        $this->assertEquals($identifierC, $superscript->getBase());
    }

    public function testSuperscript(): void
    {
        $identifierA = new Element\Identifier('a');
        $identifierB = new Element\Identifier('b');
        $identifierC = new Element\Identifier('c');

        $superscript = new Superscript($identifierA, $identifierB);

        $this->assertEquals($identifierB, $superscript->getSuperscript());
        $this->assertInstanceOf(Superscript::class, $superscript->setSuperscript($identifierC));
        $this->assertEquals($identifierC, $superscript->getSuperscript());
    }
}
