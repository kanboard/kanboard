<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Element;

use PhpOffice\Math\Element\Semantics;
use PHPUnit\Framework\TestCase;

class SemanticsTest extends TestCase
{
    public function testConstruct(): void
    {
        $semantics = new Semantics();

        $this->assertIsArray($semantics->getAnnotations());
        $this->assertCount(0, $semantics->getAnnotations());
    }

    public function testAnnotation(): void
    {
        $semantics = new Semantics();

        $this->assertIsArray($semantics->getAnnotations());
        $this->assertCount(0, $semantics->getAnnotations());

        $this->assertInstanceOf(Semantics::class, $semantics->addAnnotation('encoding', 'content'));
        $this->assertEquals(['encoding' => 'content'], $semantics->getAnnotations());
        $this->assertCount(1, $semantics->getAnnotations());

        $this->assertEquals('content', $semantics->getAnnotation('encoding'));
        $this->assertNull($semantics->getAnnotation('notexisting'));
    }
}
