<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Element;

use PhpOffice\Math\Element\Identifier;
use PHPUnit\Framework\TestCase;

class IdentifierTest extends TestCase
{
    public function testConstruct(): void
    {
        $operator = new Identifier('x');

        $this->assertEquals('x', $operator->getValue());
    }
}
