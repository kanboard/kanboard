<?php

require_once __DIR__.'/Base.php';

use Subscriber\ProjectModificationDateSubscriber;
use Model\Project;
use Model\ProjectPermission;
use Model\User;
use Model\Task;
use Model\TaskCreation;
use Model\Acl;
use Model\Board;
use Model\Config;
use Model\Category;

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

    public function testCreationWithDefaultCategories()
    {
        $p = new Project($this->container);
        $c = new Config($this->container);
        $cat = new Category($this->container);

        // Multiple categories correctly formatted

        $this->assertTrue($c->save(array('project_categories' => 'Test1, Test2')));
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);

        $categories = $cat->getAll(1);
        $this->assertNotEmpty($categories);
        $this->assertEquals(2, count($categories));
        $this->assertEquals('Test1', $categories[0]['name']);
        $this->assertEquals('Test2', $categories[1]['name']);

        // Single category

        $this->assertTrue($c->save(array('project_categories' => 'Test1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);

        $categories = $cat->getAll(2);
        $this->assertNotEmpty($categories);
        $this->assertEquals(1, count($categories));
        $this->assertEquals('Test1', $categories[0]['name']);

        // Multiple categories badly formatted

        $this->assertTrue($c->save(array('project_categories' => 'ABC, , DEF 3,  ')));
        $this->assertEquals(3, $p->create(array('name' => 'UnitTest3')));

        $project = $p->getById(3);
        $this->assertNotEmpty($project);

        $categories = $cat->getAll(3);
        $this->assertNotEmpty($categories);
        $this->assertEquals(2, count($categories));
        $this->assertEquals('ABC', $categories[0]['name']);
        $this->assertEquals('DEF 3', $categories[1]['name']);

        // No default categories
        $this->assertTrue($c->save(array('project_categories' => '  ')));
        $this->assertEquals(4, $p->create(array('name' => 'UnitTest4')));

        $project = $p->getById(4);
        $this->assertNotEmpty($project);

        $categories = $cat->getAll(4);
        $this->assertEmpty($categories);
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
        $this->assertGreaterThan($now, $project['last_modified']);
    }

    public function testIsLastModified()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $now = time();

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now, $project['last_modified']);

        sleep(1);

        $listener = new ProjectModificationDateSubscriber($this->container);
        $this->container['dispatcher']->addListener(Task::EVENT_CREATE_UPDATE, array($listener, 'execute'));

        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(Task::EVENT_CREATE_UPDATE.'.Subscriber\ProjectModificationDateSubscriber::execute', $called);

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
