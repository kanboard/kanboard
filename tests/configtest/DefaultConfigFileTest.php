<?php

namespace KanboardTests\configtest;

use PHPUnit\Framework\TestCase;

class DefaultConfigFileTest extends TestCase
{
    public function testThatFileCanBeImported()
    {
        $this->assertNotFalse(include __DIR__.'/../../config.default.php');
    }
}
