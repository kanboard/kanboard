<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\CurrencyModel;

class CurrencyTest extends Base
{
    public function testGetCurrencies()
    {
        $currencyModel = new CurrencyModel($this->container);
        $currencies = $currencyModel->getCurrencies();
        $this->assertArrayHasKey('EUR', $currencies);
    }

    public function testGetAll()
    {
        $currencyModel = new CurrencyModel($this->container);
        $currencies = $currencyModel->getAll();
        $this->assertCount(0, $currencies);

        $this->assertNotFalse($currencyModel->create('USD', 9.9));
        $currencies = $currencyModel->getAll();
        $this->assertCount(1, $currencies);
        $this->assertEquals('USD', $currencies[0]['currency']);
        $this->assertEquals(9.9, $currencies[0]['rate']);
    }

    public function testCreate()
    {
        $currencyModel = new CurrencyModel($this->container);
        $this->assertNotFalse($currencyModel->create('EUR', 1.2));
        $this->assertNotFalse($currencyModel->create('EUR', 1.5));
    }

    public function testUpdate()
    {
        $currencyModel = new CurrencyModel($this->container);
        $this->assertNotFalse($currencyModel->create('EUR', 1.1));
        $this->assertNotFalse($currencyModel->update('EUR', 2.2));
    }

    public function testGetPrice()
    {
        $currencyModel = new CurrencyModel($this->container);

        $this->assertEquals(123, $currencyModel->getPrice('USD', 123));

        $this->assertNotFalse($currencyModel->create('EUR', 0.5));
        $this->assertEquals(50.0, $currencyModel->getPrice('EUR', 100));
        $this->assertEquals(50.0, $currencyModel->getPrice('EUR', 100)); // test with cached result
    }
}
