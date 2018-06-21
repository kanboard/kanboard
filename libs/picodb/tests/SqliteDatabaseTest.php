<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use PicoDb\Database;

class SqliteDatabaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PicoDb\Database
     */
    private $db;

    public function setUp()
    {
        $this->db = new Database(array('driver' => 'sqlite', 'filename' => ':memory:'));
    }

    public function testEscapeIdentifer()
    {
        $this->assertEquals('"a"', $this->db->escapeIdentifier('a'));
        $this->assertEquals('a.b', $this->db->escapeIdentifier('a.b'));
        $this->assertEquals('"c"."a"', $this->db->escapeIdentifier('a', 'c'));
        $this->assertEquals('a.b', $this->db->escapeIdentifier('a.b', 'c'));
        $this->assertEquals('SELECT COUNT(*) FROM test', $this->db->escapeIdentifier('SELECT COUNT(*) FROM test'));
        $this->assertEquals('SELECT COUNT(*) FROM test', $this->db->escapeIdentifier('SELECT COUNT(*) FROM test', 'b'));
    }

    public function testEscapeIdentiferList()
    {
        $this->assertEquals(array('"c"."a"', '"c"."b"'), $this->db->escapeIdentifierList(array('a', 'b'), 'c'));
        $this->assertEquals(array('"a"', 'd.b'), $this->db->escapeIdentifierList(array('a', 'd.b')));
    }

    public function testThatPreparedStatementWorks()
    {
        $this->db->getConnection()->exec('CREATE TABLE foobar (id INTEGER PRIMARY KEY, something TEXT)');
        $this->db->execute('INSERT INTO foobar (something) VALUES (?)', array('a'));
        $this->assertEquals(1, $this->db->getLastId());
        $this->assertEquals('a', $this->db->execute('SELECT something FROM foobar WHERE something=?', array('a'))->fetchColumn());
    }

    /**
     * @expectedException PicoDb\SQLException
     */
    public function testBadSQLQuery()
    {
        $this->db->execute('INSERT INTO foobar');
    }

    public function testDuplicateKey()
    {
        $this->db->getConnection()->exec('CREATE TABLE foobar (something TEXT UNIQUE)');

        $this->assertNotFalse($this->db->execute('INSERT INTO foobar (something) VALUES (?)', array('a')));
        $this->assertFalse($this->db->execute('INSERT INTO foobar (something) VALUES (?)', array('a')));

        $this->assertEquals(1, $this->db->execute('SELECT COUNT(*) FROM foobar WHERE something=?', array('a'))->fetchColumn());
    }

    public function testThatTransactionReturnsAValue()
    {
        $this->assertEquals('a', $this->db->transaction(function (Database $db) {
            $db->getConnection()->exec('CREATE TABLE foobar (something TEXT UNIQUE)');
            $db->execute('INSERT INTO foobar (something) VALUES (?)', array('a'));

            return $db->execute('SELECT something FROM foobar WHERE something=?', array('a'))->fetchColumn();
        }));
    }

    public function testThatTransactionReturnsTrue()
    {
        $this->assertTrue($this->db->transaction(function (Database $db) {
            $db->getConnection()->exec('CREATE TABLE foobar (something TEXT UNIQUE)');
            $db->execute('INSERT INTO foobar (something) VALUES (?)', array('a'));
        }));
    }

    /**
     * @expectedException PicoDb\SQLException
     */
    public function testThatTransactionThrowExceptionWhenRollbacked()
    {
        $this->assertFalse($this->db->transaction(function (Database $db) {
            $db->getConnection()->exec('CREATE TABL');
        }));
    }

    public function testThatTransactionReturnsFalseWhithDuplicateKey()
    {
        $this->assertFalse($this->db->transaction(function (Database $db) {
            $db->getConnection()->exec('CREATE TABLE foobar (something TEXT UNIQUE)');
            $r1 = $db->execute('INSERT INTO foobar (something) VALUES (?)', array('a'));
            $r2 = $db->execute('INSERT INTO foobar (something) VALUES (?)', array('a'));
            return $r1 && $r2;
        }));
    }

    public function testGetInstance()
    {
        Database::setInstance('main', function () {
            return new Database(array('driver' => 'sqlite', 'filename' => ':memory:'));
        });

        $instance1 = Database::getInstance('main');
        $instance2 = Database::getInstance('main');

        $this->assertInstanceOf('PicoDb\Database', $instance1);
        $this->assertInstanceOf('PicoDb\Database', $instance2);
        $this->assertTrue($instance1 === $instance2);
    }

    /**
     * @expectedException LogicException
     */
    public function testGetMissingInstance()
    {
        Database::getInstance('notfound');
    }
}
