<?php

class StatTest extends PHPUnit_Framework_TestCase
{
    /**
     * All possible values should be > 30% but less than 170%
     * 
     * This also catches 0 and 1000
     */
    public function testDistribution()
    {
        $integers = array_fill(0, 100, 0);
        for ($i = 0; $i < 10000; ++$i) {
            ++$integers[random_int(0,99)];
        }
        for ($i = 0; $i < 100; ++$i) {
            $this->assertFalse($integers[$i] < 30);
            $this->assertFalse($integers[$i] > 170);
        }
    }
    
    /**
     * This should be between 55% and 75%, always
     */
    public function testCoverage()
    {
        $integers = array_fill(0, 2000, 0);
        for ($i = 0; $i < 2000; ++$i) {
            ++$integers[random_int(0,1999)];
        }
        $coverage = 0;
        for ($i = 0; $i < 2000; ++$i) {
            if ($integers[$i] > 0) {
                ++$coverage;
            }
        }
        $this->assertTrue($coverage >= 1150);
        $this->assertTrue($coverage <= 1350);
    }
    
    public function testCompressionRatios()
    {
        $some_bytes = random_bytes(65536);
        $compressed = gzcompress($some_bytes, 9);
        if (function_exists('mb_strlen')) {
            $length = mb_strlen($compressed, '8bit');
        } else {
            $length = strlen($compressed);
        }
        $this->assertTrue($length >= 65000 && $length <= 67000);
    }
}