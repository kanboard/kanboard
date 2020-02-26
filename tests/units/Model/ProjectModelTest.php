<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Api\Procedure\ProjectPermissionProcedure;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Translator;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\ProjectPermissionModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TagModel;
use Kanboard\Subscriber\ProjectModificationDateSubscriber;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ConfigModel;
use Kanboard\Model\CategoryModel;

class ProjectModelTest extends Base
{
    public function testCreationForAllLanguages()
    {
        $projectModel = new ProjectModel($this->container);

        foreach ($this->container['languageModel']->getLanguages() as $locale => $language) {
            Translator::unload();
            Translator::load($locale);
            $this->assertNotFalse($projectModel->create(array('name' => 'UnitTest '.$locale)), 'Unable to create project with '.$locale.':'.$language);
        }

        Translator::unload();
    }

    public function testCreation()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_active']);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEquals(0, $project['is_private']);
        $this->assertEquals(0, $project['per_swimlane_task_limits']);
        $this->assertEquals(0, $project['task_limit']);
        $this->assertEquals(time(), $project['last_modified'], '', 1);
        $this->assertEmpty($project['token']);
        $this->assertEmpty($project['start_date']);
        $this->assertEmpty($project['end_date']);
    }

    public function testCreationWithUserId()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertFalse($projectModel->create(array('name' => 'UnitTest'), 3));

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest'), 1));
        $project = $projectModel->getById(1);
        $this->assertEquals(1, $project['owner_id']);

        $this->assertEquals(2, $projectModel->create(array('name' => 'UnitTest'), 0));
        $project = $projectModel->getById(2);
        $this->assertEquals(0, $project['owner_id']);
    }

    public function testCreationWithTaskLimit()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest', 'task_limit' => 3)));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(3, $project['task_limit']);
    }

    public function testProjectDate()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertTrue($projectModel->update(array(
            'id' => 1,
            'start_date' => '2016-08-31',
            'end_date' => '08/31/2016',
        )));

        $project = $projectModel->getById(1);
        $this->assertEquals('2016-08-31', $project['start_date']);
        $this->assertEquals('2016-08-31', $project['end_date']);
    }

    public function testCreationWithDuplicateName()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'UnitTest')));
    }

    public function testCreationWithStartAndDate()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest', 'start_date' => '2015-01-01', 'end_date' => '2015-12-31')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('2015-01-01', $project['start_date']);
        $this->assertEquals('2015-12-31', $project['end_date']);
    }

    public function testCreationWithDefaultCategories()
    {
        $projectModel = new ProjectModel($this->container);
        $configModel = new ConfigModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        // Multiple categories correctly formatted

        $this->assertTrue($configModel->save(array('project_categories' => 'Test1, Test2')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest1')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);

        $categories = $categoryModel->getAll(1);
        $this->assertNotEmpty($categories);
        $this->assertEquals(2, count($categories));
        $this->assertEquals('Test1', $categories[0]['name']);
        $this->assertEquals('Test2', $categories[1]['name']);

        // Single category

        $this->assertTrue($configModel->save(array('project_categories' => 'Test1')));
        $this->container['memoryCache']->flush();
        $this->assertEquals(2, $projectModel->create(array('name' => 'UnitTest2')));

        $project = $projectModel->getById(2);
        $this->assertNotEmpty($project);

        $categories = $categoryModel->getAll(2);
        $this->assertNotEmpty($categories);
        $this->assertEquals(1, count($categories));
        $this->assertEquals('Test1', $categories[0]['name']);

        // Multiple categories badly formatted

        $this->assertTrue($configModel->save(array('project_categories' => 'ABC, , DEF 3,  ')));
        $this->container['memoryCache']->flush();
        $this->assertEquals(3, $projectModel->create(array('name' => 'UnitTest3')));

        $project = $projectModel->getById(3);
        $this->assertNotEmpty($project);

        $categories = $categoryModel->getAll(3);
        $this->assertNotEmpty($categories);
        $this->assertEquals(2, count($categories));
        $this->assertEquals('ABC', $categories[0]['name']);
        $this->assertEquals('DEF 3', $categories[1]['name']);

        // No default categories
        $this->assertTrue($configModel->save(array('project_categories' => '  ')));
        $this->container['memoryCache']->flush();
        $this->assertEquals(4, $projectModel->create(array('name' => 'UnitTest4')));

        $project = $projectModel->getById(4);
        $this->assertNotEmpty($project);

        $categories = $categoryModel->getAll(4);
        $this->assertEmpty($categories);
    }

    public function testCreationWithBlankTaskLimit()
    {
        $projectModel = new ProjectModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest1', 'task_limit' => '')));
        $project = $projectModel->getById(1);
        $this->assertEquals(0, $project['task_limit']);
    }

    public function testUpdateLastModifiedDate()
    {
        $projectModel = new ProjectModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $now = time();

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now, $project['last_modified'], 'Wrong Timestamp', 1);

        sleep(1);
        $this->assertTrue($projectModel->updateModificationDate(1));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertGreaterThan($now, $project['last_modified']);
    }

    public function testUpdateOwnerId()
    {
        $projectModel = new ProjectModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $this->assertFalse($projectModel->update(array('id'=> 1, 'name' => 'test', 'owner_id' => 2)));

        $this->assertTrue($projectModel->update(array('id'=> 1, 'name' => 'test', 'owner_id' => 1)));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['owner_id']);

        $this->assertTrue($projectModel->update(array('id'=> 1, 'name' => 'test', 'owner_id' => 0)));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['owner_id']);
    }

    public function testUpdatePerSwimlaneTaskLimits()
    {
        $projectModel = new ProjectModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $project = $projectModel->getById(1);
        $this->assertEquals(0, $project['per_swimlane_task_limits']);

        $this->assertTrue($projectModel->update(array('id'=> 1, 'per_swimlane_task_limits' => 1)));

        $project = $projectModel->getById(1);
        $this->assertEquals(1, $project['per_swimlane_task_limits']);
    }

    public function testUpdateTaskLimit()
    {
        $projectModel = new ProjectModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $project = $projectModel->getById(1);
        $this->assertEquals(0, $project['task_limit']);

        $this->assertTrue($projectModel->update(array('id'=> 1, 'task_limit' => 1)));

        $project = $projectModel->getById(1);
        $this->assertEquals(1, $project['task_limit']);
    }

    public function testGetAllIds()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $this->assertEmpty($projectModel->getAllByIds(array()));
        $this->assertNotEmpty($projectModel->getAllByIds(array(1, 2)));
        $this->assertCount(1, $projectModel->getAllByIds(array(1)));
    }

    public function testIsLastModified()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $now = time();

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals($now, $project['last_modified']);

        sleep(1);

        $listener = new ProjectModificationDateSubscriber($this->container);
        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, array($listener, 'execute'));

        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1)));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE_UPDATE.'.Kanboard\Subscriber\ProjectModificationDateSubscriber::execute', $called);

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertTrue($projectModel->isModifiedSince(1, $now));
    }

    public function testRemove()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertTrue($projectModel->remove(1));
        $this->assertFalse($projectModel->remove(1234));
    }

    public function testRemoveTagsOnProjectRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $tagModel = new TagModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertNotFalse($tagModel->create(1, 'TestTag'));

        $this->assertCount(1, $tagModel->getAllByProject(1));

        $this->assertTrue($projectModel->remove(1));

        $this->assertCount(0, $tagModel->getAllByProject(1));
    }

    public function testRemoveSwimlaneOnProjectRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $swimlaneId = $swimlaneModel->create(1, 'TestSwimlane');
        $this->assertNotFalse($swimlaneId);

        $this->assertTrue($projectModel->remove(1));
        $this->assertNull($swimlaneModel->getById($swimlaneId));
    }

    public function testRemoveColumnOnProjectRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $columnModel = new ColumnModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));

        $columnId = $columnModel->create(1, 'TestColumn');
        $this->assertNotFalse($columnId);

        $this->assertTrue($projectModel->remove(1));
        $this->assertNull($columnModel->getById($columnId));
    }

    public function testRemovePermissionOnProjectRemove()
    {
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);

        $permissionModel = new ProjectPermissionModel($this->container);
        $permissionProcedure = new ProjectPermissionProcedure($this->container);

        $userId = $userModel->create(array('username' => 'user1'));
        $this->assertNotFalse($userId);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $permissionProcedure->addProjectUser(1, $userId, Role::PROJECT_MEMBER);

        $this->assertTrue($permissionModel->isUserAllowed(1, $userId));
        $this->assertTrue($projectModel->remove(1));
        $this->assertFalse($permissionModel->isUserAllowed(1, $userId));
    }

    public function testEnable()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertTrue($projectModel->disable(1));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_active']);

        $this->assertFalse($projectModel->disable(1111));
    }

    public function testDisable()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertTrue($projectModel->disable(1));
        $this->assertTrue($projectModel->enable(1));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_active']);

        $this->assertFalse($projectModel->enable(1234567));
    }

    public function testEnablePublicAccess()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertTrue($projectModel->enablePublicAccess(1));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(1, $project['is_public']);
        $this->assertNotEmpty($project['token']);

        $this->assertFalse($projectModel->enablePublicAccess(123));
    }

    public function testDisablePublicAccess()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertTrue($projectModel->enablePublicAccess(1));
        $this->assertTrue($projectModel->disablePublicAccess(1));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals(0, $project['is_public']);
        $this->assertEmpty($project['token']);

        $this->assertFalse($projectModel->disablePublicAccess(123));
    }

    public function testIdentifier()
    {
        $projectModel = new ProjectModel($this->container);

        // Creation
        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest1', 'identifier' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'UnitTest2')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST1', $project['identifier']);

        $project = $projectModel->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('', $project['identifier']);

        // Update
        $this->assertTrue($projectModel->update(array('id' => '2', 'identifier' => 'test2')));

        $project = $projectModel->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST2', $project['identifier']);

        $project = $projectModel->getByIdentifier('test1');
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST1', $project['identifier']);

        $project = $projectModel->getByIdentifier('');
        $this->assertFalse($project);
    }

    public function testEmail()
    {
        $projectModel = new ProjectModel($this->container);

        // Creation
        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest1', 'email' => 'test1@localhost')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'UnitTest2')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('test1@localhost', $project['email']);

        $project = $projectModel->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('', $project['email']);

        // Update
        $this->assertTrue($projectModel->update(array('id' => '1', 'email' => 'test1@here')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('test1@here', $project['email']);

        $project = $projectModel->getByEmail('test1@here');
        $this->assertEquals(1, $project['id']);

        $project = $projectModel->getByEmail('test1@localhost');
        $this->assertEmpty($project);

        $project = $projectModel->getByEmail('');
        $this->assertFalse($project);
    }

    public function testThatProjectCreatorAreAlsoOwner()
    {
        $projectModel = new ProjectModel($this->container);
        $userModel = new UserModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1', 'name' => 'Me')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'My project 1'), 2));
        $this->assertEquals(2, $projectModel->create(array('name' => 'My project 2')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreationModel->create(array('title' => 'Task #2', 'project_id' => 1)));

        $project = $projectModel->getByIdWithOwnerAndTaskCount(1);
        $this->assertNotEmpty($project);
        $this->assertSame('My project 1', $project['name']);
        $this->assertSame('Me', $project['owner_name']);
        $this->assertSame('user1', $project['owner_username']);
        $this->assertEquals(2, $project['owner_id']);
        $this->assertEquals(2, $project['nb_active_tasks']);

        $project = $projectModel->getByIdWithOwnerAndTaskCount(2);
        $this->assertNotEmpty($project);
        $this->assertSame('My project 2', $project['name']);
        $this->assertEquals('', $project['owner_name']);
        $this->assertEquals('', $project['owner_username']);
        $this->assertEquals(0, $project['owner_id']);
        $this->assertEquals(0, $project['nb_active_tasks']);
    }

    public function testGetList()
    {
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project B'), 1));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project A', 'is_private' => 1), 1));

        $this->assertEquals(array(0 => 'None', 1 => 'Project B'), $projectModel->getList());
        $this->assertEquals(array(1 => 'Project B'), $projectModel->getList(false));
        $this->assertEquals(array(2 => 'Project A', 1 => 'Project B'), $projectModel->getList(false, false));
        $this->assertEquals(array(0 => 'None', 2 => 'Project A', 1 => 'Project B'), $projectModel->getList(true, false));
    }
}
