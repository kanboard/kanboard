<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Security\Role;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Model\TagModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskProjectMoveModel;
use Kanboard\Model\TaskTagModel;
use Kanboard\Model\UserModel;

class TaskProjectMoveModelTest extends Base
{
    public function onMoveProject($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals('test', $event_data['task']['title']);
    }

    public function testMoveAnotherProject()
    {
        $taskProjectMoveModel = new TaskProjectMoveModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $taskCreationModel->create(array(
            'title' => 'test',
            'project_id' => 1,
            'owner_id' => 1,
            'category_id' => 10,
            'position' => 333,
            'priority' => 1,
        )));

        $this->container['dispatcher']->addListener(TaskModel::EVENT_MOVE_PROJECT, array($this, 'onMoveProject'));

        // We duplicate our task to the 2nd project
        $this->assertTrue($taskProjectMoveModel->moveToProject(1, 2));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_MOVE_PROJECT.'.TaskProjectMoveModelTest::onMoveProject', $called);

        // Check the values of the moved task
        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(2, $task['swimlane_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['priority']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithCategory()
    {
        $taskProjectMoveModel = new TaskProjectMoveModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        $this->assertNotFalse($categoryModel->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertNotFalse($categoryModel->create(array('name' => 'Category #1', 'project_id' => 2)));
        $this->assertTrue($categoryModel->exists(1));
        $this->assertTrue($categoryModel->exists(2));

        // We create a task
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'category_id' => 1)));

        // We move our task to the 2nd project
        $this->assertTrue($taskProjectMoveModel->moveToProject(1, 2));

        // Check the values of the duplicated task
        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(2, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithUser()
    {
        $taskProjectMoveModel = new TaskProjectMoveModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        // We create a new user for our project
        $this->assertNotFalse($userModel->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(2, 2, Role::PROJECT_MEMBER));

        // We create a task
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 2)));

        // We move our task to the 2nd project
        $this->assertTrue($taskProjectMoveModel->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(6, $task['column_id']);
    }

    public function testMoveAnotherProjectWithForbiddenUser()
    {
        $taskProjectMoveModel = new TaskProjectMoveModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        // We create a new user for our project
        $this->assertNotFalse($userModel->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(2, 2, Role::PROJECT_MEMBER));

        // We create a task
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 3)));

        // We move our task to the 2nd project
        $this->assertTrue($taskProjectMoveModel->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(6, $task['column_id']);
    }

    public function testMoveAnotherProjectWithSwimlane()
    {
        $taskProjectMoveModel = new TaskProjectMoveModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(4, $swimlaneModel->create(2, 'Swimlane #1'));

        // We create a task
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 3)));

        // We move our task to the 2nd project
        $this->assertTrue($taskProjectMoveModel->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(4, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithoutSwimlane()
    {
        $taskProjectMoveModel = new TaskProjectMoveModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $swimlaneModel = new SwimlaneModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        $this->assertEquals(3, $swimlaneModel->create(1, 'Swimlane #1'));
        $this->assertEquals(4, $swimlaneModel->create(2, 'Swimlane #2'));

        // We create a task
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 3)));

        // We move our task to the 2nd project
        $this->assertTrue($taskProjectMoveModel->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(2, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithDifferentTags()
    {
        $taskProjectMoveModel = new TaskProjectMoveModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $tagModel = new TagModel($this->container);
        $taskTagModel = new TaskTagModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));

        // We create our tags for each projects
        $this->assertEquals(1, $tagModel->create(1, 'T1'));
        $this->assertEquals(2, $tagModel->create(1, 'T2'));
        $this->assertEquals(3, $tagModel->create(2, 'T3'));
        $this->assertEquals(4, $tagModel->create(2, 'T4'));
        $this->assertEquals(5, $tagModel->create(0, 'T5'));
        $this->assertEquals(6, $tagModel->create(0, 'T6'));

        // We create a task
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'test', 'project_id' => 1, 'tags' => array('T1', 'T5', 'T6'))));

        // We move our task to the 2nd project
        $this->assertTrue($taskProjectMoveModel->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $taskFinderModel->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['project_id']);

        // Check tags
        $tags = $taskTagModel->getList(1);
        $this->assertCount(3, $tags);
        $this->assertArrayHasKey(5, $tags);
        $this->assertArrayHasKey(6, $tags);
        $this->assertArrayHasKey(7, $tags);
    }
}
