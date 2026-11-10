<?php

namespace KanboardTests\units\Core\Session;

use KanboardTests\units\Base;
use Kanboard\Core\Session\SessionHandler;

class SessionHandlerTest extends Base
{
    public function testWriteCreatesSession()
    {
        $handler = new SessionHandler($this->container['db']);
        $data = serialize(['SomeKey' => 'SomeValue']);

        $this->assertSame('', $handler->read('SomeSessionId'));
        $this->assertTrue($handler->write('SomeSessionId', $data));
        $this->assertSame($data, $handler->read('SomeSessionId'));
        $this->assertEquals(1, $this->container['db']->table(SessionHandler::TABLE)->count());
    }

    public function testWriteUpdatesSession()
    {
        $handler = new SessionHandler($this->container['db']);
        $this->assertTrue($handler->write('SecondSessionId', serialize(['SomeKey' => 'SomeValue'])));

        $this->container['db']->table(SessionHandler::TABLE)->eq('id', 'SecondSessionId')->update(['expire_at' => 1]);

        $data = serialize(['SomeKey' => 'SomeUpdatedValue']);
        $this->assertTrue($handler->write('SecondSessionId', $data));
        $this->assertSame($data, $handler->read('SecondSessionId'));

        $expireAt = $this->container['db']->table(SessionHandler::TABLE)->eq('id', 'SecondSessionId')->findOneColumn('expire_at');
        $this->assertGreaterThan(time(), (int) $expireAt);
        $this->assertEquals(1, $this->container['db']->table(SessionHandler::TABLE)->count());
    }

    public function testWriteLeavesOtherSessionsAlone()
    {
        $handler = new SessionHandler($this->container['db']);
        $first = serialize(['testkey' => '1']);
        $second = serialize(['testkey' => '2']);

        $this->assertTrue($handler->write('s1', $first));
        $this->assertTrue($handler->write('s2', $second));

        $this->assertSame($first, $handler->read('s1'));
        $this->assertSame($second, $handler->read('s2'));
    }
}
