<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Subtask;
use Kanboard\Model\Project;
use Kanboard\Model\Category;
use Kanboard\Model\User;
use Kanboard\Core\Session;
use Kanboard\Model\UserSession;

class SubtaskTest extends Base
{
    public function onSubtaskCreated($event)
    {
        $this->assertInstanceOf('Kanboard\Event\SubtaskEvent', $event);
        $data = $event->getAll();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('time_estimated', $data);
        $this->assertArrayHasKey('time_spent', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('task_id', $data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('position', $data);
        $this->assertNotEmpty($data['task_id']);
        $this->assertNotEmpty($data['id']);
    }

    public function onSubtaskUpdated($event)
    {
        $this->assertInstanceOf('Kanboard\Event\SubtaskEvent', $event);
        $data = $event->getAll();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('time_estimated', $data);
        $this->assertArrayHasKey('time_spent', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('task_id', $data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('position', $data);
        $this->assertArrayHasKey('changes', $data);
        $this->assertArrayHasKey('user_id', $data['changes']);
        $this->assertArrayHasKey('status', $data['changes']);

        $this->assertEquals(Subtask::STATUS_INPROGRESS, $data['changes']['status']);
        $this->assertEquals(1, $data['changes']['user_id']);
    }

    public function onSubtaskDeleted($event)
    {
        $this->assertInstanceOf('Kanboard\Event\SubtaskEvent', $event);
        $data = $event->getAll();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('time_estimated', $data);
        $this->assertArrayHasKey('time_spent', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('task_id', $data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('position', $data);
        $this->assertNotEmpty($data['task_id']);
        $this->assertNotEmpty($data['id']);
    }

    public function testCreation()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->container['dispatcher']->addListener(Subtask::EVENT_CREATE, array($this, 'onSubtaskCreated'));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['id']);
        $this->assertEquals(1, $subtask['task_id']);
        $this->assertEquals('subtask #1', $subtask['title']);
        $this->assertEquals(Subtask::STATUS_TODO, $subtask['status']);
        $this->assertEquals(0, $subtask['time_estimated']);
        $this->assertEquals(0, $subtask['time_spent']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['position']);
    }

    public function testModification()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->container['dispatcher']->addListener(Subtask::EVENT_UPDATE, array($this, 'onSubtaskUpdated'));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertTrue($s->update(array('id' => 1, 'user_id' => 1, 'status' => Subtask::STATUS_INPROGRESS)));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['id']);
        $this->assertEquals(1, $subtask['task_id']);
        $this->assertEquals('subtask #1', $subtask['title']);
        $this->assertEquals(Subtask::STATUS_INPROGRESS, $subtask['status']);
        $this->assertEquals(0, $subtask['time_estimated']);
        $this->assertEquals(0, $subtask['time_spent']);
        $this->assertEquals(1, $subtask['user_id']);
        $this->assertEquals(1, $subtask['position']);
    }

    public function testRemove()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));
        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $this->container['dispatcher']->addListener(Subtask::EVENT_DELETE, array($this, 'onSubtaskDeleted'));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);

        $this->assertTrue($s->remove(1));

        $subtask = $s->getById(1);
        $this->assertEmpty($subtask);
    }

    public function testToggleStatusWithoutSession()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(Subtask::STATUS_TODO, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertTrue($s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(Subtask::STATUS_INPROGRESS, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertTrue($s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(Subtask::STATUS_DONE, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertTrue($s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(Subtask::STATUS_TODO, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);
    }

    public function testToggleStatusWithSession()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);
        $ss = new Session;
        $us = new UserSession($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(Subtask::STATUS_TODO, $subtask['status']);
        $this->assertEquals(0, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        // Set the current logged user
        $ss['user'] = array('id' => 1);

        $this->assertTrue($s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(Subtask::STATUS_INPROGRESS, $subtask['status']);
        $this->assertEquals(1, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertTrue($s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(Subtask::STATUS_DONE, $subtask['status']);
        $this->assertEquals(1, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);

        $this->assertTrue($s->toggleStatus(1));

        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(Subtask::STATUS_TODO, $subtask['status']);
        $this->assertEquals(1, $subtask['user_id']);
        $this->assertEquals(1, $subtask['task_id']);
    }

    public function testCloseAll()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));

        $this->assertTrue($s->closeAll(1));

        $subtasks = $s->getAll(1);
        $this->assertNotEmpty($subtasks);

        foreach ($subtasks as $subtask) {
            $this->assertEquals(Subtask::STATUS_DONE, $subtask['status']);
        }
    }

    public function testMoveUp()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));
        $this->assertEquals(3, $s->create(array('title' => 'subtask #3', 'task_id' => 1)));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(3, $subtask['position']);

        // Move up
        $this->assertTrue($s->moveUp(1, 2));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(3, $subtask['position']);

        // We can't move up #2
        $this->assertFalse($s->moveUp(1, 2));

        // Test remove
        $this->assertTrue($s->remove(1));
        $this->assertTrue($s->moveUp(1, 3));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertEmpty($subtask);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);
    }

    public function testMoveDown()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1)));

        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1)));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1)));
        $this->assertEquals(3, $s->create(array('title' => 'subtask #3', 'task_id' => 1)));

        // Move down #1
        $this->assertTrue($s->moveDown(1, 1));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(3, $subtask['position']);

        // We can't move down #3
        $this->assertFalse($s->moveDown(1, 3));

        // Test remove
        $this->assertTrue($s->remove(1));
        $this->assertTrue($s->moveDown(1, 2));

        // Check positions
        $subtask = $s->getById(1);
        $this->assertEmpty($subtask);

        $subtask = $s->getById(2);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(2, $subtask['position']);

        $subtask = $s->getById(3);
        $this->assertNotEmpty($subtask);
        $this->assertEquals(1, $subtask['position']);
    }

    public function testDuplicate()
    {
        $tc = new TaskCreation($this->container);
        $s = new Subtask($this->container);
        $p = new Project($this->container);

        // We create a project
        $this->assertEquals(1, $p->create(array('name' => 'test1')));

        // We create 2 tasks
        $this->assertEquals(1, $tc->create(array('title' => 'test 1', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 1)));
        $this->assertEquals(2, $tc->create(array('title' => 'test 2', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 0)));

        // We create many subtasks for the first task
        $this->assertEquals(1, $s->create(array('title' => 'subtask #1', 'task_id' => 1, 'time_estimated' => 5, 'time_spent' => 3, 'status' => 1, 'another_subtask' => 'on')));
        $this->assertEquals(2, $s->create(array('title' => 'subtask #2', 'task_id' => 1, 'time_estimated' => '', 'time_spent' => '', 'status' => 2, 'user_id' => 1)));

        // We duplicate our subtasks
        $this->assertTrue($s->duplicate(1, 2));
        $subtasks = $s->getAll(2);

        $this->assertNotFalse($subtasks);
        $this->assertNotEmpty($subtasks);
        $this->assertEquals(2, count($subtasks));

        $this->assertEquals('subtask #1', $subtasks[0]['title']);
        $this->assertEquals('subtask #2', $subtasks[1]['title']);

        $this->assertEquals(2, $subtasks[0]['task_id']);
        $this->assertEquals(2, $subtasks[1]['task_id']);

        $this->assertEquals(5, $subtasks[0]['time_estimated']);
        $this->assertEquals(0, $subtasks[1]['time_estimated']);

        $this->assertEquals(0, $subtasks[0]['time_spent']);
        $this->assertEquals(0, $subtasks[1]['time_spent']);

        $this->assertEquals(0, $subtasks[0]['status']);
        $this->assertEquals(0, $subtasks[1]['status']);

        $this->assertEquals(0, $subtasks[0]['user_id']);
        $this->assertEquals(0, $subtasks[1]['user_id']);

        $this->assertEquals(1, $subtasks[0]['position']);
        $this->assertEquals(2, $subtasks[1]['position']);
    }
}
