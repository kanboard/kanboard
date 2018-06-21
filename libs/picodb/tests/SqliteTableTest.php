<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use PicoDb\Database;
use PicoDb\Table;

class SqliteTableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PicoDb\Database
     */
    private $db;

    public function setUp()
    {
        $this->db = new Database(array('driver' => 'sqlite', 'filename' => ':memory:'));
    }

    public function testSelect()
    {
        $this->assertEquals('SELECT 1 FROM "test"', $this->db->table('test')->select(1)->buildSelectQuery());
    }

    public function testColumns()
    {
        $this->assertEquals('SELECT "a", "b" FROM "test"', $this->db->table('test')->columns('a', 'b')->buildSelectQuery());
    }

    public function testDistinct()
    {
        $this->assertEquals('SELECT DISTINCT "a", "b" FROM "test"', $this->db->table('test')->distinct('a', 'b')->buildSelectQuery());
    }

    public function testGroupBy()
    {
        $this->assertEquals('SELECT * FROM "test"   GROUP BY "a"', $this->db->table('test')->groupBy('a')->buildSelectQuery());
    }

    public function testOrderBy()
    {
        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" ASC', $this->db->table('test')->asc('a')->buildSelectQuery());
        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" ASC', $this->db->table('test')->orderBy('a', Table::SORT_ASC)->buildSelectQuery());

        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" DESC', $this->db->table('test')->desc('a')->buildSelectQuery());
        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" DESC', $this->db->table('test')->orderBy('a', Table::SORT_DESC)->buildSelectQuery());

        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" ASC, "b" ASC', $this->db->table('test')->asc('a')->asc('b')->buildSelectQuery());
        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" DESC, "b" DESC', $this->db->table('test')->desc('a')->desc('b')->buildSelectQuery());

        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" ASC, "b" ASC', $this->db->table('test')->orderBy('a')->orderBy('b')->buildSelectQuery());
        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" DESC, "b" DESC', $this->db->table('test')->orderBy('a', Table::SORT_DESC)->orderBy('b', Table::SORT_DESC)->buildSelectQuery());

        $this->assertEquals('SELECT * FROM "test"     ORDER BY "a" DESC, "b" ASC', $this->db->table('test')->desc('a')->asc('b')->buildSelectQuery());
    }

    public function testLimit()
    {
        $this->assertEquals('SELECT * FROM "test"      LIMIT 10', $this->db->table('test')->limit(10)->buildSelectQuery());
        $this->assertEquals('SELECT * FROM "test"', $this->db->table('test')->limit(null)->buildSelectQuery());
    }

    public function testOffset()
    {
        $this->assertEquals('SELECT * FROM "test"       OFFSET 0', $this->db->table('test')->offset(0)->buildSelectQuery());
        $this->assertEquals('SELECT * FROM "test"       OFFSET 10', $this->db->table('test')->offset(10)->buildSelectQuery());
        $this->assertEquals('SELECT * FROM "test"', $this->db->table('test')->limit(null)->buildSelectQuery());
    }

    public function testLimitOffset()
    {
        $this->assertEquals('SELECT * FROM "test"      LIMIT 2  OFFSET 0', $this->db->table('test')->offset(0)->limit(2)->buildSelectQuery());
        $this->assertEquals('SELECT * FROM "test"      LIMIT 5  OFFSET 10', $this->db->table('test')->offset(10)->limit(5)->buildSelectQuery());
    }

    public function testSubquery()
    {
        $this->assertEquals('SELECT (SELECT 1 FROM "foobar" WHERE 1=1) AS "b" FROM "test"', $this->db->table('test')->subquery('SELECT 1 FROM "foobar" WHERE 1=1', 'b')->buildSelectQuery());
    }

    public function testConditionEqual()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" = ? AND "b" = ?', $table->eq('a', 2)->eq('b', 'foobar')->buildSelectQuery());
        $this->assertEquals(array(2, 'foobar'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionNotEqual()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" != ?', $table->neq('a', 2)->buildSelectQuery());
        $this->assertEquals(array(2), $table->getConditionBuilder()->getValues());
    }

    public function testConditionIn()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" IN (?, ?)', $table->in('a', array('b', 'c'))->buildSelectQuery());
        $this->assertEquals(array('b', 'c'), $table->getConditionBuilder()->getValues());

        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"', $table->in('a', array())->buildSelectQuery());
        $this->assertEquals(array(), $table->getConditionBuilder()->getValues());
    }

    public function testConditionInSubquery()
    {
        $table = $this->db->table('test');
        $subquery = $this->db->table('test2')->columns('c')->eq('d', 'e');

        $this->assertEquals(
            'SELECT * FROM "test"   WHERE "a" IN (SELECT "c" FROM "test2"   WHERE "d" = ?)',
            $table->inSubquery('a', $subquery)->buildSelectQuery()
        );

        $this->assertEquals(array('e'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionNotIn()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" NOT IN (?, ?)', $table->notIn('a', array('b', 'c'))->buildSelectQuery());
        $this->assertEquals(array('b', 'c'), $table->getConditionBuilder()->getValues());

        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"', $table->notIn('a', array())->buildSelectQuery());
        $this->assertEquals(array(), $table->getConditionBuilder()->getValues());
    }

    public function testConditionNotInSubquery()
    {
        $table = $this->db->table('test');
        $subquery = $this->db->table('test2')->columns('c')->eq('d', 'e');

        $this->assertEquals(
            'SELECT * FROM "test"   WHERE "a" NOT IN (SELECT "c" FROM "test2"   WHERE "d" = ?)',
            $table->notInSubquery('a', $subquery)->buildSelectQuery()
        );

        $this->assertEquals(array('e'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionLike()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" LIKE ?', $table->like('a', '%foobar%')->buildSelectQuery());
        $this->assertEquals(array('%foobar%'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionILike()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" LIKE ?', $table->ilike('a', '%foobar%')->buildSelectQuery());
        $this->assertEquals(array('%foobar%'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionGreaterThan()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" > ?', $table->gt('a', 5)->buildSelectQuery());
        $this->assertEquals(array(5), $table->getConditionBuilder()->getValues());
    }

    public function testConditionGreaterThanInSubquery()
    {
        $table = $this->db->table('test');
        $subquery = $this->db->table('test2')->columns('c')->eq('d', 'e');

        $this->assertEquals(
            'SELECT * FROM "test"   WHERE "a" > (SELECT "c" FROM "test2"   WHERE "d" = ?)',
            $table->gtSubquery('a', $subquery)->buildSelectQuery()
        );

        $this->assertEquals(array('e'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionGreaterThanOrEqual()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" >= ?', $table->gte('a', 5)->buildSelectQuery());
        $this->assertEquals(array(5), $table->getConditionBuilder()->getValues());
    }

    public function testConditionGreaterThanEqualInSubquery()
    {
        $table = $this->db->table('test');
        $subquery = $this->db->table('test2')->columns('c')->eq('d', 'e');

        $this->assertEquals(
            'SELECT * FROM "test"   WHERE "a" >= (SELECT "c" FROM "test2"   WHERE "d" = ?)',
            $table->gteSubquery('a', $subquery)->buildSelectQuery()
        );

        $this->assertEquals(array('e'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionLowerThan()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" < ?', $table->lt('a', 5)->buildSelectQuery());
        $this->assertEquals(array(5), $table->getConditionBuilder()->getValues());
    }

    public function testConditionLowerThanInSubquery()
    {
        $table = $this->db->table('test');
        $subquery = $this->db->table('test2')->columns('c')->eq('d', 'e');

        $this->assertEquals(
            'SELECT * FROM "test"   WHERE "a" < (SELECT "c" FROM "test2"   WHERE "d" = ?)',
            $table->ltSubquery('a', $subquery)->buildSelectQuery()
        );

        $this->assertEquals(array('e'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionLowerThanOrEqual()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" <= ?', $table->lte('a', 5)->buildSelectQuery());
        $this->assertEquals(array(5), $table->getConditionBuilder()->getValues());
    }

    public function testConditionLowerThanEqualInSubquery()
    {
        $table = $this->db->table('test');
        $subquery = $this->db->table('test2')->columns('c')->eq('d', 'e');

        $this->assertEquals(
            'SELECT * FROM "test"   WHERE "a" <= (SELECT "c" FROM "test2"   WHERE "d" = ?)',
            $table->lteSubquery('a', $subquery)->buildSelectQuery()
        );

        $this->assertEquals(array('e'), $table->getConditionBuilder()->getValues());
    }

    public function testConditionIsNull()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" IS NOT NULL', $table->notNull('a')->buildSelectQuery());
        $this->assertEquals(array(), $table->getConditionBuilder()->getValues());
    }

    public function testCustomCondition()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE a=c AND "b" = ?', $table->addCondition('a=c')->eq('b', 4)->buildSelectQuery());
        $this->assertEquals(array(4), $table->getConditionBuilder()->getValues());
    }

    public function testOrConditions()
    {
        $table = $this->db->table('test');

        $this->assertEquals('SELECT * FROM "test"   WHERE "a" IS NOT NULL AND ("b" = ? OR "c" >= ?)', $table->notNull('a')->beginOr()->eq('b', 2)->gte('c', 5)->closeOr()->buildSelectQuery());
        $this->assertEquals(array(2, 5), $table->getConditionBuilder()->getValues());
    }

    public function testMultipleOrConditions()
    {
        $table = $this->db->table('test');

        $this->assertEquals(
            'SELECT * FROM "test"   WHERE "a" IS NOT NULL AND ("b" = ? OR ("b" != ? OR "c" = ?) OR "c" >= ?)',
            $table
                ->notNull('a')
                ->beginOr()
                    ->eq('b', 2)
                    ->beginOr()
                        ->neq('b', 6)
                        ->eq('c', 3)
                    ->closeOr()
                    ->gte('c', 5)
                ->closeOr()
                ->buildSelectQuery()
        );

        $this->assertEquals(array(2, 6, 3, 5), $table->getConditionBuilder()->getValues());
    }

    public function testPersist()
    {
        $this->assertNotFalse($this->db->execute('CREATE TABLE foobar_persist (id INTEGER PRIMARY KEY, a TEXT)'));
        $this->assertSame(1, $this->db->table('foobar_persist')->persist(array('a' => 'b')));
    }

    public function testInsertUpdate()
    {
        $this->assertNotFalse($this->db->execute('CREATE TABLE foobar (a TEXT)'));
        $this->assertTrue($this->db->table('foobar')->insert(array('a' => 'b')));
        $this->assertTrue($this->db->table('foobar')->insert(array('a' => 'c')));

        $this->assertEquals(array(array('a' => 'b'), array('a' => 'c')), $this->db->table('foobar')->findAll());

        $this->assertEquals(array('b', 'c'), $this->db->table('foobar')->findAllByColumn('a'));

        $this->assertEquals(array('a' => 'b'), $this->db->table('foobar')->findOne());

        $this->assertEquals('b', $this->db->table('foobar')->findOneColumn('a'));

        $this->assertTrue($this->db->table('foobar')->exists());
        $this->assertTrue($this->db->table('foobar')->eq('a', 'c')->exists());
        $this->assertFalse($this->db->table('foobar')->eq('a', 'e')->exists());

        $this->assertEquals(2, $this->db->table('foobar')->count());
        $this->assertEquals(1, $this->db->table('foobar')->eq('a', 'c')->count());
        $this->assertEquals(0, $this->db->table('foobar')->eq('a', 'e')->count());

        $this->assertTrue($this->db->table('foobar')->eq('a', 'c')->remove());
        $this->assertFalse($this->db->table('foobar')->eq('a', 'e')->remove());

        $this->assertTrue($this->db->table('foobar')->eq('a', 'b')->update(array('a' => 'test')));
        $this->assertTrue($this->db->table('foobar')->eq('a', 'lol')->update(array('a' => 'test')));

        $this->assertNotEmpty($this->db->table('foobar')->eq('a', 'test')->findOne());
        $this->assertNull($this->db->table('foobar')->eq('a', 'lol')->findOne());

        $this->assertTrue($this->db->table('foobar')->eq('a', 'test')->save(array('a' => 'plop')));
        $this->assertEquals(1, $this->db->table('foobar')->count());
    }

    public function testSumColumn()
    {
        $this->assertNotFalse($this->db->execute('CREATE TABLE foobar (b REAL, c REAL)'));
        $this->assertTrue($this->db->table('foobar')->insert(array('b' => 2, 'c' => 3.3)));

        $this->assertTrue($this->db->table('foobar')->sumColumn('b', 2.5)->sumColumn('c', 3)->update());

        $this->assertEquals(
            array('b' => 4.5, 'c' => 6.3),
            $this->db->table('foobar')->findOne()
        );
    }

    public function testCallback()
    {
        $this->assertNotFalse($this->db->execute('CREATE TABLE foobar (a TEXT)'));
        $this->assertTrue($this->db->table('foobar')->insert(array('a' => 'b')));
        $this->assertTrue($this->db->table('foobar')->insert(array('a' => 'c')));

        $func = function () {
            return array('test');
        };

        $this->assertEquals(array('test'), $this->db->table('foobar')->callback($func)->findAll());
        $this->assertEquals(array('plop'), $this->db->table('foobar')->callback(array($this, 'myCallback'))->findAll());
    }

    public function myCallback(array $records)
    {
        $this->assertEquals(array(array('a' => 'b'), array('a' => 'c')), $records);
        return array('plop');
    }

    public function testSum()
    {
        $this->assertNotFalse($this->db->execute('CREATE TABLE foobar (a INTEGER)'));
        $this->assertTrue($this->db->table('foobar')->insert(array('a' => 2)));
        $this->assertTrue($this->db->table('foobar')->insert(array('a' => 5)));
        $this->assertEquals(7, $this->db->table('foobar')->sum('a'));
    }

    public function testIncrement()
    {
        $this->assertNotFalse($this->db->execute('CREATE TABLE foobar (a INTEGER DEFAULT 0, b INTEGER DEFAULT 0)'));
        $this->assertTrue($this->db->table('foobar')->insert(array('a' => 2, 'b' => 5)));
        $this->assertTrue($this->db->table('foobar')->eq('b', 5)->increment('a', 3));
        $this->assertEquals(5, $this->db->table('foobar')->findOneColumn('a'));
    }

    public function testLeftJoin()
    {
        $this->assertNotFalse($this->db->execute('CREATE TABLE test1 (a INTEGER NOT NULL, foreign_key INTEGER NOT NULL)'));
        $this->assertNotFalse($this->db->execute('CREATE TABLE test2 (id INTEGER NOT NULL, b INTEGER NOT NULL)'));

        $this->assertTrue($this->db->table('test2')->insert(array('id' => 42, 'b' => 2)));
        $this->assertTrue($this->db->table('test1')->insert(array('a' => 18, 'foreign_key' => 42)));

        $this->assertEquals(
            array('a' => 18, 'b' => 2),
            $this->db->table('test2')->columns('a', 'b')->eq('a', 18)->left('test1', 't1', 'foreign_key', 'test2', 'id')->findOne()
        );

        $this->assertEquals(
            array('a' => 18, 'b' => 2),
            $this->db->table('test2')->columns('a', 'b')->eq('a', 18)->join('test1', 'foreign_key', 'id')->findOne()
        );

        $this->assertEquals(
            array('a' => 18, 'b' => 2),
            $this->db->table('test1')->columns('a', 'b')->join('test2', 'id', 'foreign_key')->findOne()
        );
    }

    public function testHashTable()
    {
        $this->assertNotFalse($this->db->execute(
            'CREATE TABLE toto (
                column1 TEXT NOT NULL UNIQUE,
                column2 TEXT default NULL
            )'
        ));

        $this->assertTrue($this->db->table('toto')->insert(array('column1' => 'option1', 'column2' => 'value1')));
        $this->assertTrue($this->db->table('toto')->insert(array('column1' => 'option2', 'column2' => 'value2')));
        $this->assertTrue($this->db->table('toto')->insert(array('column1' => 'option3', 'column2' => 'value3')));

        $values = array(
            'option1' => 'hey',
            'option4' => 'ho',
        );

        $this->assertTrue($this->db->hashtable('toto')->columnKey('column1')->columnValue('column2')->put($values));

        $this->assertEquals(
            array('option2' => 'value2', 'option4' => 'ho'),
            $this->db->hashtable('toto')->columnKey('column1')->columnValue('column2')->get('option2', 'option4')
        );

        $this->assertEquals(
            array('option2' => 'value2', 'option3' => 'value3', 'option1' => 'hey', 'option4' => 'ho'),
            $this->db->hashtable('toto')->columnKey('column1')->columnValue('column2')->get()
        );

        $this->assertEquals(
            array('option2' => 'value2', 'option3' => 'value3', 'option1' => 'hey', 'option4' => 'ho'),
            $this->db->hashtable('toto')->getAll('column1', 'column2')
        );
    }
}
