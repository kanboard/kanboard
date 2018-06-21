<?php

namespace JsonRPC\Response;

use PHPUnit_Framework_TestCase;

require_once __DIR__.'/../../../../vendor/autoload.php';

function header($value)
{
    HeaderMockTest::$functions->header($value);
}

abstract class HeaderMockTest extends PHPUnit_Framework_TestCase
{
    public static $functions;

    public function setUp()
    {
        self::$functions = $this
            ->getMockBuilder('stdClass')
            ->setMethods(array('header'))
            ->getMock();
    }
}
