<?php

require_once __DIR__.'/Base.php';

use Model\Project;
use Model\ProjectPermission;
use Model\User;
use Model\Task;
use Model\Acl;
use Model\Board;

class ProjectTest extends Base
{
    public function testCreation()
    {
        $p = new Project($this->registry);

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
        $p = new Project($this->registry);
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
        $p = new Project($this->registry);
        $t = new Task($this->registry);

        $now = time();
        $p->attachEvents();

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now, $project['last_modified']);

        sleep(1);

        $this->assertEquals(1, $t->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertTrue($this->registry->shared('event')->isEventTriggered(Task::EVENT_CREATE));
        $this->assertEquals('Event\ProjectModificationDate', $this->registry->shared('event')->getLastListenerExecuted());

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertTrue($p->isModifiedSince(1, $now));
    }

    public function testRemove()
    {
        $p = new Project($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->remove(1));
        $this->assertFalse($p->remove(1234));
    }

    public function testEnable()
    {
        $p = new Project($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->disable(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_active']);

        $this->assertFalse($p->disable(1111));
    }

    public function testDisable()
    {
        $p = new Project($this->registry);

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
        $p = new Project($this->registry);

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
        $p = new Project($this->registry);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->enablePublicAccess(1));
        $this->assertTrue($p->disablePublicAccess(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEmpty($project['token']);

        $this->assertFalse($p->disablePublicAccess(123));
    }

    public function testDuplicate()
    {
        $p = new Project($this->registry);

        // Clone public project
        $this->assertEquals(1, $p->create(array('name' => 'Public')));
        $this->assertEquals(2, $p->duplicate(1));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('Public (Clone)', $project['name']);
        $this->assertEquals(0, $project['is_private']);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEmpty($project['token']);

        // Clone private project
        $this->assertEquals(3, $p->create(array('name' => 'Private', 'is_private' => 1), 1));
        $this->assertEquals(4, $p->duplicate(3));

        $project = $p->getById(4);
        $this->assertNotEmpty($project);
        $this->assertEquals('Private (Clone)', $project['name']);
        $this->assertEquals(1, $project['is_private']);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEmpty($project['token']);

        $pp = new ProjectPermission($this->registry);

        $this->assertEquals(array(1 => 'admin'), $pp->getAllowedUsers(3));
        $this->assertEquals(array(1 => 'admin'), $pp->getAllowedUsers(4));
    }
}
