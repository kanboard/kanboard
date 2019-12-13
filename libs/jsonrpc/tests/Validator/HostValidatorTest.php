<?php

use JsonRPC\Validator\HostValidator;

require_once __DIR__.'/../../../../vendor/autoload.php';

class HostValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testWithEmptyHosts()
    {
        $this->assertNull(HostValidator::validate(array(), '127.0.0.1', '127.0.0.1'));
    }

    public function testWithValidHosts()
    {
        $this->assertNull(HostValidator::validate(array('127.0.0.1'), '127.0.0.1', '127.0.0.1'));
    }

    public function testWithValidNetwork()
    {
        $this->assertNull(HostValidator::validate(array('192.168.10.1/24'), '192.168.10.1'),'test ip match');
        $this->assertNull(HostValidator::validate(array('192.168.10.1/24'), '192.168.10.250'),'test ip match');
        $this->expectException('\JsonRPC\Exception\AccessDeniedException');
        HostValidator::validate(array('192.168.10.1/24'), '192.168.11.1');
    }

    public function testWithNotAuthorizedHosts()
    {
        $this->expectException('\JsonRPC\Exception\AccessDeniedException');
        HostValidator::validate(array('192.168.1.1'), '127.0.0.1', '127.0.0.1');
    }
}
