<?php
class UtilityTest extends PHPUnit_Framework_TestCase
{
    public function testStrlen()
    {
        if (!function_exists('RandomCompat_strlen')) {
            return $this->markTestSkipped(
                'We don\' need to test this in PHP 7.'
            );
        }
        $this->assertEquals(RandomCompat_strlen("\xF0\x9D\x92\xB3"), 4);
    }
    
    public function testIntval()
    {
        if (!function_exists('RandomCompat_intval')) {
            return $this->markTestSkipped(
                'We don\' need to test this in PHP 7.'
            );
        }
        // Equals
        $this->assertEquals(
            abs(RandomCompat_intval(-4.5)),
            abs(RandomCompat_intval(4.5))
        );
        
        // True
        $this->assertTrue(
            is_int(RandomCompat_intval(PHP_INT_MAX, true))
        );
        $this->assertTrue(
            is_int(RandomCompat_intval(~PHP_INT_MAX, true))
        );
        $this->assertTrue(
            is_int(RandomCompat_intval(~PHP_INT_MAX + 1, true))
        );
        $this->assertTrue(
            is_int(RandomCompat_intval("1337e3", true))
        );
        $this->assertTrue(
            is_int(RandomCompat_intval("1.", true))
        );
        
        // False
        $this->assertFalse(
            is_int(RandomCompat_intval((float) PHP_INT_MAX, true))
        );
        $this->assertFalse(
            is_int(RandomCompat_intval((float) ~PHP_INT_MAX, true))
        );
        $this->assertFalse(
            is_int(RandomCompat_intval(PHP_INT_MAX + 1, true))
        );
        $this->assertFalse(
            is_int(RandomCompat_intval(~PHP_INT_MAX - 1, true))
        );
        $this->assertFalse(
            is_int(RandomCompat_intval(~PHP_INT_MAX - 0.1, true))
        );
        $this->assertFalse(
            is_int(RandomCompat_intval(PHP_INT_MAX + 0.1, true))
        );
        $this->assertFalse(
            is_int(RandomCompat_intval("hello", true))
        );
        
        if (PHP_INT_SIZE === 8) {
            $this->assertFalse(
                is_int(RandomCompat_intval("-9223372036854775809", true))
            );
            $this->assertTrue(
                is_int(RandomCompat_intval("-9223372036854775808", true))
            );
            $this->assertFalse(
                is_int(RandomCompat_intval("9223372036854775808", true))
            );
            $this->assertTrue(
                is_int(RandomCompat_intval("9223372036854775807", true))
            );
        } else {
            $this->assertFalse(
                is_int(RandomCompat_intval("2147483648", true))
            );
            $this->assertTrue(
                is_int(RandomCompat_intval("2147483647", true))
            );
            $this->assertFalse(
                is_int(RandomCompat_intval("-2147483649", true))
            );
            $this->assertTrue(
                is_int(RandomCompat_intval("-2147483648", true))
            );
        }
    }
}
