<?php

use PicoDb\UrlParser;

require_once __DIR__.'/../../../vendor/autoload.php';

class UrlParserTest extends PHPUnit_Framework_TestCase
{
    public function testParseUrl()
    {
        $urlParser = UrlParser::getInstance();
        $this->assertFalse($urlParser->isEnvironmentVariableDefined());

        $settings = $urlParser->getSettings('postgres://user:pass@hostname:6212/db');
        $this->assertEquals('postgres', $settings['driver']);
        $this->assertEquals('user', $settings['username']);
        $this->assertEquals('pass', $settings['password']);
        $this->assertEquals('hostname', $settings['hostname']);
        $this->assertEquals('6212', $settings['port']);
        $this->assertEquals('db', $settings['database']);
    }

    public function testParseWrongUrl()
    {
        $urlParser = new UrlParser();
        $settings = $urlParser->getSettings('/');
        $this->assertEmpty($settings['driver']);
        $this->assertFalse($urlParser->isEnvironmentVariableDefined());
    }

    public function testGetUrlFromEnvironment()
    {
        putenv('DATABASE_URL=postgres://user:pass@hostname:6212/db');

        $urlParser = new UrlParser();
        $this->assertTrue($urlParser->isEnvironmentVariableDefined());

        $settings = $urlParser->getSettings();
        $this->assertEquals('postgres', $settings['driver']);
        $this->assertEquals('user', $settings['username']);
        $this->assertEquals('pass', $settings['password']);
        $this->assertEquals('hostname', $settings['hostname']);
        $this->assertEquals('6212', $settings['port']);
        $this->assertEquals('db', $settings['database']);
    }
}
