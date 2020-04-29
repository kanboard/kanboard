<?php

class DefaultConfigFileTest extends PHPUnit\Framework\TestCase
{
    public function testThatFileCanBeImported()
    {
        $this->assertNotFalse(include __DIR__.'/../../config.default.php');
    }
}
