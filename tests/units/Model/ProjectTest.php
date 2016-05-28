<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Translator;
use Kanboard\Subscriber\ProjectModificationDateSubscriber;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ConfigModel;
use Kanboard\Model\CategoryModel;

class ProjectTest extends Base
{
    public function testCreationForAllLanguages()
    {
        $p = new ProjectModel($this->container);

        foreach ($this->container['languageModel']->getLanguages() as $locale => $language) {
            Translator::unload();
            Translator::load($locale);
            $this->assertNotFalse($p->create(array('name' => 'UnitTest '.$locale)), 'Unable to create project with '.$locale.':'.$language);
        }

        Translator::unload();
    }

    public function testCreation()
    {
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_active']);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEquals(0, $project['is_private']);
        $this->assertEquals(time(), $project['last_modified'], '', 1);
        $this->assertEmpty($project['token']);
    }

    public function testCreationWithDuplicateName()
    {
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest')));
    }

    public function testCreationWithStartAndDate()
    {
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest', 'start_date' => '2015-01-01', 'end_date' => '2015-12-31')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('2015-01-01', $project['start_date']);
        $this->assertEquals('2015-12-31', $project['end_date']);
    }

    public function testCreationWithDefaultCategories()
    {
        $p = new ProjectModel($this->container);
        $c = new ConfigModel($this->container);
        $cat = new CategoryModel($this->container);

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
        $this->container['memoryCache']->flush();
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);

        $categories = $cat->getAll(2);
        $this->assertNotEmpty($categories);
        $this->assertEquals(1, count($categories));
        $this->assertEquals('Test1', $categories[0]['name']);

        // Multiple categories badly formatted

        $this->assertTrue($c->save(array('project_categories' => 'ABC, , DEF 3,  ')));
        $this->container['memoryCache']->flush();
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
        $this->container['memoryCache']->flush();
        $this->assertEquals(4, $p->create(array('name' => 'UnitTest4')));

        $project = $p->getById(4);
        $this->assertNotEmpty($project);

        $categories = $cat->getAll(4);
        $this->assertEmpty($categories);
    }

    public function testUpdateLastModifiedDate()
    {
        $p = new ProjectModel($this->container);
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $now = time();

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now, $project['last_modified'], 'Wrong Timestamp', 1);

        sleep(1);
        $this->assertTrue($p->updateModificationDate(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertGreaterThan($now, $project['last_modified']);
    }

    public function testGetAllIds()
    {
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $this->assertEmpty($p->getAllByIds(array()));
        $this->assertNotEmpty($p->getAllByIds(array(1, 2)));
        $this->assertCount(1, $p->getAllByIds(array(1)));
    }

    public function testIsLastModified()
    {
        $p = new ProjectModel($this->container);
        $tc = new TaskCreationModel($this->container);

        $now = time();

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now, $project['last_modified']);

        sleep(1);

        $listener = new ProjectModificationDateSubscriber($this->container);
        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, array($listener, 'execute'));

        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE_UPDATE.'.Kanboard\Subscriber\ProjectModificationDateSubscriber::execute', $called);

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertTrue($p->isModifiedSince(1, $now));
    }

    public function testRemove()
    {
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->remove(1));
        $this->assertFalse($p->remove(1234));
    }

    public function testEnable()
    {
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->disable(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_active']);

        $this->assertFalse($p->disable(1111));
    }

    public function testDisable()
    {
        $p = new ProjectModel($this->container);

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
        $p = new ProjectModel($this->container);

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
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertTrue($p->enablePublicAccess(1));
        $this->assertTrue($p->disablePublicAccess(1));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEmpty($project['token']);

        $this->assertFalse($p->disablePublicAccess(123));
    }

    public function testIdentifier()
    {
        $p = new ProjectModel($this->container);

        // Creation
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1', 'identifier' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST1', $project['identifier']);

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('', $project['identifier']);

        // Update
        $this->assertTrue($p->update(array('id' => '2', 'identifier' => 'test2')));

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST2', $project['identifier']);

        $project = $p->getByIdentifier('test1');
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST1', $project['identifier']);

        $project = $p->getByIdentifier('');
        $this->assertFalse($project);
    }

    public function testThatProjectCreatorAreAlsoOwner()
    {
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'Me')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'My project 1'), 2));
        $this->assertEquals(2, $projectModel->create(array('name' => 'My project 2')));

        $project = $projectModel->getByIdWithOwner(1);
        $this->assertNotEmpty($project);
        $this->assertSame('My project 1', $project['name']);
        $this->assertSame('Me', $project['owner_name']);
        $this->assertSame('user1', $project['owner_username']);
        $this->assertEquals(2, $project['owner_id']);

        $project = $projectModel->getByIdWithOwner(2);
        $this->assertNotEmpty($project);
        $this->assertSame('My project 2', $project['name']);
        $this->assertEquals('', $project['owner_name']);
        $this->assertEquals('', $project['owner_username']);
        $this->assertEquals(0, $project['owner_id']);
    }

    public function testPriority()
    {
        $projectModel = new ProjectModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'My project 2')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['priority_default']);
        $this->assertEquals(0, $project['priority_start']);
        $this->assertEquals(3, $project['priority_end']);

        $this->assertEquals(
            array(0 => 0, 1 => 1, 2 => 2, 3 => 3),
            $projectModel->getPriorities($project)
        );

        $this->assertTrue($projectModel->update(array('id' => 1, 'priority_start' => 2, 'priority_end' => 5, 'priority_default' => 4)));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(4, $project['priority_default']);
        $this->assertEquals(2, $project['priority_start']);
        $this->assertEquals(5, $project['priority_end']);

        $this->assertEquals(
            array(2 => 2, 3 => 3, 4 => 4, 5 => 5),
            $projectModel->getPriorities($project)
        );
    }
}
