<?php

require_once __DIR__.'/Base.php';

use Model\Project;
use Model\ProjectPermission;
use Model\User;
use Model\Task;
use Model\TaskCreation;
use Model\Acl;
use Model\Board;

class ProjectTest extends Base
{
    public function testCreation()
    {
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_active']);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEquals(0, $project['is_private']);
        $this->assertEquals(time(), $project['last_modified']);
        $this->assertEmpty($project['token']);
    }

    public function testUpdateLastModifiedDate()
    {
        $p = new Project($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $now = time();

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now, $project['last_modified']);

        sleep(1);
        $this->assertTrue($p->updateModificationDate(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now + 1, $project['last_modified']);
    }

    public function testIsLastModified()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $now = time();
        $p->attachEvents();

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now, $project['last_modified']);

        sleep(1);

        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertTrue($this->container['event']->isEventTriggered(Task::EVENT_CREATE));
        $this->assertEquals('Event\ProjectModificationDateListener', $this->container['event']->getLastListenerExecuted());

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertTrue($p->isModifiedSince(1, $now));
    }

    public function testRemove()
    {
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->remove(1));
        $this->assertFalse($p->remove(1234));
    }

    public function testEnable()
    {
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->disable(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_active']);

        $this->assertFalse($p->disable(1111));
    }

    public function testDisable()
    {
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->disable(1));
        $this->assertTrue($p->enable(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_active']);

        $this->assertFalse($p->enable(1234567));
    }

    public function testEnablePublicAccess()
    {
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->enablePublicAccess(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_public']);
        $this->assertNotEmpty($project['token']);

        $this->assertFalse($p->enablePublicAccess(123));
    }

    public function testDisablePublicAccess()
    {
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->enablePublicAccess(1));
        $this->assertTrue($p->disablePublicAccess(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEmpty($project['token']);

        $this->assertFalse($p->disablePublicAccess(123));
    }
}
