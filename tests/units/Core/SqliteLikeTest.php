<?php

namespace KanboardTests\units\Core;

use KanboardTests\units\Base;

class SqliteLikeTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();

        if (DB_DRIVER !== 'sqlite') {
            $this->markTestSkipped('Sqlite only');
        }
    }

    public function testUnicodeCaseInsensitivity()
    {
        $this->assertSame(1, $this->query("SELECT 'Änderung' LIKE 'änderung%'"));
        $this->assertSame(1, $this->query("SELECT 'änderung' LIKE 'ÄNDERUNG'"));
        $this->assertSame(1, $this->query("SELECT 'Привет мир' LIKE '%ПРИВЕТ%'"));
        $this->assertSame(1, $this->query("SELECT 'abc' LIKE 'ABC'"));
        $this->assertSame(0, $this->query("SELECT 'abc' LIKE 'abd'"));
        $this->assertSame(0, $this->query("SELECT 'x' NOT LIKE 'X'"));
    }

    public function testWildcards()
    {
        $this->assertSame(1, $this->query("SELECT 'ä' LIKE '_'"));
        $this->assertSame(0, $this->query("SELECT 'äb' LIKE '_'"));
        $this->assertSame(1, $this->query("SELECT 'line1' || char(10) || 'line2' LIKE '%line2'"));
        $this->assertSame(0, $this->query("SELECT 'abc' LIKE 'a.c'"));
        $this->assertSame(0, $this->query("SELECT 'a.c' LIKE 'abc'"));
        $this->assertSame(1, $this->query("SELECT CAST(123 AS CHAR(8)) LIKE '12%'"));
    }

    public function testEscape()
    {
        $this->assertSame(1, $this->query("SELECT '50%' LIKE '50!%' ESCAPE '!'"));
        $this->assertSame(0, $this->query("SELECT '50x' LIKE '50!%' ESCAPE '!'"));
    }

    public function testNull()
    {
        $this->assertNull($this->query("SELECT NULL LIKE 'x'"));
        $this->assertNull($this->query("SELECT 'x' LIKE NULL"));
    }

    private function query($sql)
    {
        $value = $this->container['db']->getConnection()->query($sql)->fetchColumn();
        return $value === null ? null : (int) $value;
    }
}
