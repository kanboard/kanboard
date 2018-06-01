<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Event\GenericEvent;

class DummyAction extends Kanboard\Action\Base
{
    public function getDescription()
    {
        return 'Dummy Action';
    }

    public function getCompatibleEvents()
    {
        return array('my.event');
    }

    public function getActionRequiredParameters()
    {
        return array('p1' => 'Param 1');
    }

    public function getEventRequiredParameters()
    {
        return array('p1', 'p2', 'p3' => array('p4'));
    }

    public function doAction(array $data)
    {
        return true;
    }

    public function hasRequiredCondition(array $data)
    {
        return $data['p1'] == $this->getParam('p1');
    }
}

class BaseActionTest extends Base
{
    public function testGetName()
    {
        $dummyAction = new DummyAction($this->container);
        $this->assertEquals('\\DummyAction', $dummyAction->getName());
    }

    public function testGetDescription()
    {
        $dummyAction = new DummyAction($this->container);
        $this->assertEquals('Dummy Action', $dummyAction->getDescription());
    }

    public function testGetActionRequiredParameters()
    {
        $dummyAction = new DummyAction($this->container);
        $this->assertEquals(array('p1' => 'Param 1'), $dummyAction->getActionRequiredParameters());
    }

    public function testGetEventRequiredParameters()
    {
        $dummyAction = new DummyAction($this->container);
        $this->assertEquals(array('p1', 'p2', 'p3' => array('p4')), $dummyAction->getEventRequiredParameters());
    }

    public function testGetCompatibleEvents()
    {
        $dummyAction = new DummyAction($this->container);
        $this->assertEquals(array('my.event'), $dummyAction->getCompatibleEvents());
    }

    public function testHasRequiredCondition()
    {
        $dummyAction = new DummyAction($this->container);
        $dummyAction->setParam('p1', 123);
        $this->assertTrue($dummyAction->hasRequiredCondition(array('p1' => 123)));
        $this->assertFalse($dummyAction->hasRequiredCondition(array('p1' => 456)));
    }

    public function testProjectId()
    {
        $dummyAction = new DummyAction($this->container);
        $this->assertInstanceOf('DummyAction', $dummyAction->setProjectId(123));
        $this->assertEquals(123, $dummyAction->getProjectId());
    }

    public function testParam()
    {
        $dummyAction = new DummyAction($this->container);
        $this->assertInstanceOf('DummyAction', $dummyAction->setParam('p1', 123));
        $this->assertEquals(123, $dummyAction->getParam('p1'));
    }

    public function testHasCompatibleEvents()
    {
        $dummyAction = new DummyAction($this->container);
        $this->assertTrue($dummyAction->hasCompatibleEvent('my.event'));
        $this->assertFalse($dummyAction->hasCompatibleEvent('foobar'));
    }

    public function testHasRequiredProject()
    {
        $dummyAction = new DummyAction($this->container);
        $dummyAction->setProjectId(1234);

        $this->assertTrue($dummyAction->hasRequiredProject(array('project_id' => 1234)));
        $this->assertFalse($dummyAction->hasRequiredProject(array('project_id' => 1)));
        $this->assertFalse($dummyAction->hasRequiredProject(array()));
    }

    public function testHasRequiredParameters()
    {
        $dummyAction = new DummyAction($this->container);
        $dummyAction->setProjectId(1234);

        $this->assertTrue($dummyAction->hasRequiredParameters(array('p1' => 12, 'p2' => 34, 'p3' => array('p4' => 'foobar'))));
        $this->assertFalse($dummyAction->hasRequiredParameters(array('p1' => 12)));
        $this->assertFalse($dummyAction->hasRequiredParameters(array()));
    }

    public function testAddEvent()
    {
        $dummyAction = new DummyAction($this->container);
        $dummyAction->addEvent('foobar', 'FooBar');
        $dummyAction->addEvent('my.event', 'My Event Overrided');

        $events = $dummyAction->getEvents();
        $this->assertCount(2, $events);
        $this->assertEquals(array('my.event', 'foobar'), $events);
    }

    public function testExecuteOnlyOnce()
    {
        $dummyAction = new DummyAction($this->container);
        $dummyAction->setProjectId(1234);
        $dummyAction->setParam('p1', 'something');
        $dummyAction->addEvent('foobar', 'FooBar');

        $event = new GenericEvent(array('project_id' => 1234, 'p1' => 'something', 'p2' => 'abc', 'p3' => array('p4' => 'a')));

        $this->assertTrue($dummyAction->execute($event, 'foobar'));
        $this->assertFalse($dummyAction->execute($event, 'foobar'));
    }

    public function testExecuteSameActionSeveralTimesWithDifferentEvents()
    {
        $dummyAction = new DummyAction($this->container);
        $dummyAction->setProjectId(1234);
        $dummyAction->setParam('p1', 'something');
        $dummyAction->addEvent('foobar', 'FooBar');

        $event1 = new GenericEvent(array('project_id' => 1234, 'p1' => 'something', 'p2' => 'abc', 'p3' => array('p4' => 'a')));
        $event2 = new GenericEvent(array('project_id' => 1234, 'p1' => 'something', 'p2' => 'abc', 'p3' => array('p4' => 'b')));

        $this->assertTrue($dummyAction->execute($event1, 'foobar'));
        $this->assertTrue($dummyAction->execute($event2, 'foobar'));
    }
}
