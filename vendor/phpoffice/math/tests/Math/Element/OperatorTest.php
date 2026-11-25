<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Element;

use PhpOffice\Math\Element\Operator;
use PHPUnit\Framework\TestCase;

class OperatorTest extends TestCase
{
    public function testConstruct(): void
    {
        $operator = new Operator('+');

        $this->assertEquals('+', $operator->getValue());
    }
}
