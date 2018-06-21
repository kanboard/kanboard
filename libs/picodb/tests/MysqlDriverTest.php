<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use PicoDb\Driver\Mysql;

class MysqlDriverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PicoDb\Driver\Mysql
     */
    private $driver;

    public function setUp()
    {
        $this->driver = new Mysql(array('hostname' => 'localhost', 'username' => 'root', 'password' => '', 'database' => 'picodb'));
        $this->driver->getConnection()->exec('CREATE DATABASE IF NOT EXISTS `picodb`');
        $this->driver->getConnection()->exec('DROP TABLE IF EXISTS foobar');
        $this->driver->getConnection()->exec('DROP TABLE IF EXISTS schema_version');
    }

    /**
     * @expectedException LogicException
     */
    public function testMissingRequiredParameter()
    {
        new Mysql(array());
    }

    public function testDuplicateKeyError()
    {
        $this->assertFalse($this->driver->isDuplicateKeyError(1234));
        $this->assertTrue($this->driver->isDuplicateKeyError(23000));
    }

    public function testOperator()
    {
        $this->assertEquals('LIKE BINARY', $this->driver->getOperator('LIKE'));
        $this->assertEquals('LIKE', $this->driver->getOperator('ILIKE'));
        $this->assertEquals('', $this->driver->getOperator('FOO'));
    }

    public function testSchemaVersion()
    {
        $this->assertEquals(0, $this->driver->getSchemaVersion());

        $this->driver->setSchemaVersion(1);
        $this->assertEquals(1, $this->driver->getSchemaVersion());

        $this->driver->setSchemaVersion(42);
        $this->assertEquals(42, $this->driver->getSchemaVersion());
    }

    public function testLastInsertId()
    {
        $this->assertEquals(0, $this->driver->getLastId());

        $this->driver->getConnection()->exec('CREATE TABLE foobar (id INT AUTO_INCREMENT NOT NULL, something TEXT, PRIMARY KEY (id)) ENGINE=InnoDB');
        $this->driver->getConnection()->exec('INSERT INTO foobar (something) VALUES (1)');

        $this->assertEquals(1, $this->driver->getLastId());
    }

    public function testEscape()
    {
        $this->assertEquals('`foobar`', $this->driver->escape('foobar'));
    }

    public function testDatabaseVersion()
    {
        $this->assertStringStartsWith('5.', $this->driver->getDatabaseVersion());
    }
}
