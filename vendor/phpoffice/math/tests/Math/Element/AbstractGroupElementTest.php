<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Element;

use PhpOffice\Math\Element;
use PHPUnit\Framework\TestCase;

class AbstractGroupElementTest extends TestCase
{
    public function testConstruct(): void
    {
        $row = new Element\Row();

        $this->assertIsArray($row->getElements());
        $this->assertCount(0, $row->getElements());
    }

    public function testAdd(): void
    {
        $identifierA = new Element\Identifier('a');
        $row = new Element\Row();

        $this->assertCount(0, $row->getElements());

        $this->assertInstanceOf(Element\AbstractGroupElement::class, $row->add($identifierA));

        $this->assertCount(1, $row->getElements());
        $this->assertEquals([$identifierA], $row->getElements());
    }

    public function testRemove(): void
    {
        $identifierA = new Element\Identifier('a');

        $row = new Element\Row();
        $row->add($identifierA);

        $this->assertCount(1, $row->getElements());

        $this->assertInstanceOf(Element\AbstractGroupElement::class, $row->remove($identifierA));

        $this->assertCount(0, $row->getElements());
    }
}
