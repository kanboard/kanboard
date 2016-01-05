<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Event\EventManager;

class EventManagerTest extends Base
{
    public function testAddEvent()
    {
        $eventManager = new EventManager;
        $eventManager->register('my.event', 'My Event');

        $events = $eventManager->getAll();
        $this->assertArrayHasKey('my.event', $events);
        $this->assertEquals('My Event', $events['my.event']);
    }
}
