<?php

use JsonRPC\Validator\JsonFormatValidator;

require_once __DIR__.'/../../../../vendor/autoload.php';

class JsonFormatValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testJsonParsedCorrectly()
    {
        $this->assertNull(JsonFormatValidator::validate(array('foobar')));
    }

    public function testJsonNotParsedCorrectly()
    {
        $this->expectException('\JsonRPC\Exception\InvalidJsonFormatException');
        JsonFormatValidator::validate('');
    }
}
