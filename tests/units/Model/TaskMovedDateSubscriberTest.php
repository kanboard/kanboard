<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskPosition;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\Project;
use Kanboard\Model\Swimlane;
use Kanboard\Subscriber\TaskMovedDateSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TaskMovedDateSubscriberTest extends Base
{
    public function testMoveTaskAnotherColumn()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);

        $this->container['dispatcher'] = new EventDispatcher;
        $this->container['dispatcher']->addSubscriber(new TaskMovedDateSubscriber($this->container));

        $now = time();

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals($now, $task['date_moved'], '', 1);

        sleep(1);

        $this->assertTrue($tp->movePosition(1, 1, 2, 1));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotEquals($now, $task['date_moved']);
    }

    public function testMoveTaskAnotherSwimlane()
    {
        $tp = new TaskPosition($this->container);
        $tc = new TaskCreation($this->container);
        $p = new Project($this->container);
        $tf = new TaskFinder($this->container);
        $s = new Swimlane($this->container);

        $this->container['dispatcher'] = new EventDispatcher;
        $this->container['dispatcher']->addSubscriber(new TaskMovedDateSubscriber($this->container));

        $now = time();

        $this->assertEquals(1, $p->create(array('name' => 'Project #1')));
        $this->assertEquals(1, $s->create(array('project_id' => 1, 'name' => 'S1')));
        $this->assertEquals(2, $s->create(array('project_id' => 1, 'name' => 'S2')));
        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals($now, $task['date_moved'], '', 1);
        $this->assertEquals(1, $task['column_id']);
        $this->assertEquals(0, $task['swimlane_id']);

        sleep(1);

        $this->assertTrue($tp->movePosition(1, 1, 2, 1, 2));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertNotEquals($now, $task['date_moved']);
        $this->assertEquals(2, $task['column_id']);
        $this->assertEquals(2, $task['swimlane_id']);
    }
}
