<?php

require_once __DIR__.'/../../Base.php';

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

        $class = $this
            ->getMockBuilder('stdClass')
            ->setMethods(array('doSomething'))
            ->getMock();

        $class
            ->expects($this->once())
            ->method('doSomething')
            ->with(
                $this->equalTo(1),
                $this->equalTo(2)
            )
            ->will($this->returnValue(3));

        // First call will store the computed value
        $this->assertEquals(3, $c->proxy($class, 'doSomething', 1, 2));

        // Second call get directly the cached value
        $this->assertEquals(3, $c->proxy($class, 'doSomething', 1, 2));
    }
}
