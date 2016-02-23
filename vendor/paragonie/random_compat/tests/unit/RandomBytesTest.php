<?php
class RandomBytesTest extends PHPUnit_Framework_TestCase
{
    public function testFuncExists()
    {
        $this->assertTrue(function_exists('random_bytes'));
    }
    
    public function testOutput()
    {
        $bytes = array(
            random_bytes(12),
            random_bytes(64),
            random_bytes(64),
            random_bytes(1.5)
        );
        
        $this->assertTrue(
            strlen(bin2hex($bytes[0])) === 24
        );
        $this->assertTrue(
            strlen(bin2hex($bytes[3])) === 2
        );
        
        // This should never generate identical byte strings
        $this->assertFalse(
            $bytes[1] === $bytes[2]
        );
        
        try {
            $x = random_bytes(~PHP_INT_MAX - 1000000000);
            $this->assertTrue(false);
        } catch (TypeError $ex) {
            $this->assertTrue(true);
        } catch (Error $ex) {
            $this->assertTrue(true);
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }
        
        try {
            $x = random_bytes(PHP_INT_MAX + 1000000000);
            $this->assertTrue(false);
        } catch (TypeError $ex) {
            $this->assertTrue(true);
        } catch (Error $ex) {
            $this->assertTrue(true);
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }
    }
}
