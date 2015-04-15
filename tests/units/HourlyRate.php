<?php

require_once __DIR__.'/Base.php';

use Model\User;
use Model\HourlyRate;

class HourlyRateTest extends Base
{
    public function testCreation()
    {
        $hr = new HourlyRate($this->container);
        $this->assertEquals(1, $hr->create(1, 32.4, 'EUR', '2015-01-01'));
        $this->assertEquals(2, $hr->create(1, 42, 'CAD', '2015-02-01'));

        $rates = $hr->getAllByUser(0);
        $this->assertEmpty($rates);

        $rates = $hr->getAllByUser(1);
        $this->assertNotEmpty($rates);
        $this->assertCount(2, $rates);

        $this->assertEquals(42, $rates[0]['rate']);
        $this->assertEquals('CAD', $rates[0]['currency']);
        $this->assertEquals('2015-02-01', date('Y-m-d', $rates[0]['date_effective']));

        $this->assertEquals(32.4, $rates[1]['rate']);
        $this->assertEquals('EUR', $rates[1]['currency']);
        $this->assertEquals('2015-01-01', date('Y-m-d', $rates[1]['date_effective']));

        $this->assertEquals(0, $hr->getCurrentRate(0));
        $this->assertEquals(42, $hr->getCurrentRate(1));

        $this->assertTrue($hr->remove(2));
        $this->assertEquals(32.4, $hr->getCurrentRate(1));

        $this->assertTrue($hr->remove(1));
        $this->assertEquals(0, $hr->getCurrentRate(1));

        $rates = $hr->getAllByUser(1);
        $this->assertEmpty($rates);
    }
}
