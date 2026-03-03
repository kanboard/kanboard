<?php

namespace KanboardTests\units\Core\Cache;

use KanboardTests\units\Base;
use Kanboard\Core\Cache\MemoryCache;

class MemoryCacheTest extends Base
{
    public function testKeyNotFound()
    {
        $c = new MemoryCache;
        $this->assertEquals(null, $c->get('mykey'));
    }

    public function testSetValue()
    {
        $c = new MemoryCache;
        $c->set('mykey', 'myvalue');
        $this->assertEquals('myvalue', $c->get('mykey'));
    }

    public function testRemoveValue()
    {
        $c = new MemoryCache;
        $c->set('mykey', 'myvalue');
        $c->remove('mykey');
        $this->assertEquals(null, $c->get('mykey'));
    }

    public function testFlushAll()
    {
        $c = new MemoryCache;
        $c->set('mykey', 'myvalue');
        $c->flush();
        $this->assertEquals(null, $c->get('mykey'));
    }

    public function testProxy()
    {
        $c = new MemoryCache;

        $class = new class {
            public $calls = 0;

            public function doSomething($a, $b)
            {
                $this->calls++;
                return $a + $b;
            }
        };

        // First call will store the computed value
        $this->assertEquals(3, $c->proxy($class, 'doSomething', 1, 2));

        // Second call get directly the cached value
        $this->assertEquals(3, $c->proxy($class, 'doSomething', 1, 2));
        $this->assertEquals(1, $class->calls);
    }
}
