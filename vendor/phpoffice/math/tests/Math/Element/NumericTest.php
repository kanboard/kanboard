<?php

declare(strict_types=1);

namespace Tests\PhpOffice\Math\Element;

use PhpOffice\Math\Element\Numeric;
use PHPUnit\Framework\TestCase;

class NumericTest extends TestCase
{
    public function testConstruct(): void
    {
        $numeric = new Numeric(2);

        $this->assertEquals(2, $numeric->getValue());
    }
}
