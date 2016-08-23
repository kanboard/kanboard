<?php

class DefaultConfigFileTest extends PHPUnit_Framework_TestCase
{
    public function testThatFileCanBeImported()
    {
        $this->assertNotFalse(include __DIR__.'/../../config.default.php');
    }
}
