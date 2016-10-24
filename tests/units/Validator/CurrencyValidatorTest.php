<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\CurrencyValidator;

class CurrencyValidatorTest extends Base
{
    public function testValidation()
    {
        $currencyValidator = new CurrencyValidator($this->container);
        $result = $currencyValidator->validateCreation(array());
        $this->assertFalse($result[0]);

        $result = $currencyValidator->validateCreation(array('currency' => 'EUR'));
        $this->assertFalse($result[0]);

        $result = $currencyValidator->validateCreation(array('rate' => 1.9));
        $this->assertFalse($result[0]);

        $result = $currencyValidator->validateCreation(array('currency' => 'EUR', 'rate' => 'foobar'));
        $this->assertFalse($result[0]);

        $result = $currencyValidator->validateCreation(array('currency' => 'EUR', 'rate' => 1.25));
        $this->assertTrue($result[0]);
    }
}
