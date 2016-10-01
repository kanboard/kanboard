<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\CurrencyValidator;

class CurrencyValidatorTest extends Base
{
    public function testValidation()
    {
        $validator = new CurrencyValidator($this->container);
        $result = $validator->validateCreation(array());
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array('currency' => 'EUR'));
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array('rate' => 1.9));
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array('currency' => 'EUR', 'rate' => 'foobar'));
        $this->assertFalse($result[0]);

        $result = $validator->validateCreation(array('currency' => 'EUR', 'rate' => 1.25));
        $this->assertTrue($result[0]);
    }
}
