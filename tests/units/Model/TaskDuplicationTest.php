<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\DateParser;
use Kanboard\Model\TaskModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskDuplicationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\UserModel;
use Kanboard\Model\SwimlaneModel;
use Kanboard\Core\Security\Role;

class TaskDuplicationTest extends Base
{
    public function testThatDuplicateDefineCreator()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1)));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(0, $task['creator_id']);

        $this->container['sessionStorage']->user = array('id' => 1);

        // We duplicate our task
        $this->assertEquals(2, $td->duplicate(1));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['creator_id']);
    }

    public function testDuplicateSameProject()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $c = new CategoryModel($this->container);

        // We create a task and a project
        $this->assertEquals(1, $p->create(array('name' => 'test1')));

        // Some categories
        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertNotFalse($c->create(array('name' => 'Category #2', 'project_id' => 1)));
        $this->assertTrue($c->exists(1));
        $this->assertTrue($c->exists(2));

        $this->assertEquals(
            1,
            $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 3, 'owner_id' => 1, 'category_id' => 2, 'time_spent' => 4.4)
        ));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(4.4, $task['time_spent']);

        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, function () {});
        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE, function () {});

        // We duplicate our task
        $this->assertEquals(2, $td->duplicate(1));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE.'.closure', $called);

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::STATUS_OPEN, $task['is_active']);
        $this->assertEquals(1, $task['project_id']);
        $this->assertEquals(1, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(3, $task['column_id']);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals('test', $task['title']);
        $this->assertEquals(0, $task['time_spent']);
    }

    public function testDuplicateAnotherProject()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $c = new CategoryModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertTrue($c->exists(1));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 1, 'category_id' => 1)));

        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE_UPDATE, function () {});
        $this->container['dispatcher']->addListener(TaskModel::EVENT_CREATE, function () {});

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE_UPDATE.'.closure', $called);
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE.'.closure', $called);

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testDuplicateAnotherProjectWithCategory()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $c = new CategoryModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 2)));
        $this->assertTrue($c->exists(1));
        $this->assertTrue($c->exists(2));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'category_id' => 1)));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testDuplicateAnotherProjectWithPredefinedCategory()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $c = new CategoryModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 2)));
        $this->assertNotFalse($c->create(array('name' => 'Category #2', 'project_id' => 2)));
        $this->assertTrue($c->exists(1));
        $this->assertTrue($c->exists(2));
        $this->assertTrue($c->exists(3));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'category_id' => 1)));

        // We duplicate our task to the 2nd project with no category
        $this->assertEquals(2, $td->duplicateToProject(1, 2, null, null, 0));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['category_id']);

        // We duplicate our task to the 2nd project with a different category
        $this->assertEquals(3, $td->duplicateToProject(1, 2, null, null, 3));

        // Check the values of the duplicated task
        $task = $tf->getById(3);
        $this->assertNotEmpty($task);
        $this->assertEquals(3, $task['category_id']);
    }

    public function testDuplicateAnotherProjectWithSwimlane()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $s = new SwimlaneModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertNotFalse($s->create(array('project_id' => 2, 'name' => 'Swimlane #1')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 1)));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(2, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testDuplicateAnotherProjectWithoutSwimlane()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $s = new SwimlaneModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertNotFalse($s->create(array('project_id' => 2, 'name' => 'Swimlane #2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 1)));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testDuplicateAnotherProjectWithPredefinedSwimlane()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $s = new SwimlaneModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertNotFalse($s->create(array('project_id' => 2, 'name' => 'Swimlane #1')));
        $this->assertNotFalse($s->create(array('project_id' => 2, 'name' => 'Swimlane #2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 1)));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2, 3));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(3, $task['swimlane_id']);
    }

    public function testDuplicateAnotherProjectWithPredefinedColumn()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2)));

        // We duplicate our task to the 2nd project with a different column
        $this->assertEquals(2, $td->duplicateToProject(1, 2, null, 7));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(7, $task['column_id']);
    }

    public function testDuplicateAnotherProjectWithUser()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $pp = new ProjectUserRoleModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 2)));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);

        // We create a new user for our project
        $user = new UserModel($this->container);
        $this->assertNotFalse($user->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertTrue($pp->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($pp->addUser(2, 2, Role::PROJECT_MEMBER));

        // We duplicate our task to the 2nd project
        $this->assertEquals(3, $td->duplicateToProject(1, 2));

        // Check the values of the duplicated task
        $task = $tf->getById(3);
        $this->assertNotEmpty($task);
        $this->assertEquals(2, $task['position']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(2, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);

        // We duplicate a task with a not allowed user
        $this->assertEquals(4, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 1, 'owner_id' => 3)));
        $this->assertEquals(5, $td->duplicateToProject(4, 2));

        $task = $tf->getById(5);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
    }

    public function testDuplicateAnotherProjectWithPredefinedUser()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $pr = new ProjectUserRoleModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 2)));
        $this->assertTrue($pr->addUser(2, 1, Role::PROJECT_MEMBER));

        // We duplicate our task to the 2nd project
        $this->assertEquals(2, $td->duplicateToProject(1, 2, null, null, null, 1));

        // Check the values of the duplicated task
        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['owner_id']);
    }

    public function onMoveProject($event)
    {
        $this->assertInstanceOf('Kanboard\Event\TaskEvent', $event);

        $event_data = $event->getAll();
        $this->assertNotEmpty($event_data);
        $this->assertEquals(1, $event_data['task_id']);
        $this->assertEquals('test', $event_data['title']);
    }

    public function testMoveAnotherProject()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $user = new UserModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'owner_id' => 1, 'category_id' => 10, 'position' => 333)));

        $this->container['dispatcher']->addListener(TaskModel::EVENT_MOVE_PROJECT, array($this, 'onMoveProject'));

        // We duplicate our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        $called = $this->container['dispatcher']->getCalledListeners();
        $this->assertArrayHasKey(TaskModel::EVENT_MOVE_PROJECT.'.TaskDuplicationTest::onMoveProject', $called);

        // Check the values of the moved task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(5, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithCategory()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $c = new CategoryModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertNotFalse($c->create(array('name' => 'Category #1', 'project_id' => 2)));
        $this->assertTrue($c->exists(1));
        $this->assertTrue($c->exists(2));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'category_id' => 1)));

        // We move our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the duplicated task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['category_id']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithUser()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $pp = new ProjectUserRoleModel($this->container);
        $user = new UserModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a new user for our project
        $this->assertNotFalse($user->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertTrue($pp->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($pp->addUser(2, 2, Role::PROJECT_MEMBER));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 2)));

        // We move our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(6, $task['column_id']);
    }

    public function testMoveAnotherProjectWithForbiddenUser()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $pp = new ProjectUserRoleModel($this->container);
        $user = new UserModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        // We create a new user for our project
        $this->assertNotFalse($user->create(array('username' => 'unittest#1', 'password' => 'unittest')));
        $this->assertTrue($pp->addUser(1, 2, Role::PROJECT_MEMBER));
        $this->assertTrue($pp->addUser(2, 2, Role::PROJECT_MEMBER));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'owner_id' => 3)));

        // We move our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals(6, $task['column_id']);
    }

    public function testMoveAnotherProjectWithSwimlane()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $s = new SwimlaneModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertNotFalse($s->create(array('project_id' => 2, 'name' => 'Swimlane #1')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 1)));

        // We move our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(2, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testMoveAnotherProjectWithoutSwimlane()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $s = new SwimlaneModel($this->container);

        // We create 2 projects
        $this->assertEquals(1, $p->create(array('name' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'test2')));

        $this->assertNotFalse($s->create(array('project_id' => 1, 'name' => 'Swimlane #1')));
        $this->assertNotFalse($s->create(array('project_id' => 2, 'name' => 'Swimlane #2')));

        // We create a task
        $this->assertEquals(1, $tc->create(array('title' => 'test', 'project_id' => 1, 'column_id' => 2, 'swimlane_id' => 1)));

        // We move our task to the 2nd project
        $this->assertTrue($td->moveToProject(1, 2));

        // Check the values of the moved task
        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(0, $task['owner_id']);
        $this->assertEquals(0, $task['category_id']);
        $this->assertEquals(0, $task['swimlane_id']);
        $this->assertEquals(6, $task['column_id']);
        $this->assertEquals(1, $task['position']);
        $this->assertEquals(2, $task['project_id']);
        $this->assertEquals('test', $task['title']);
    }

    public function testCalculateRecurringTaskDueDate()
    {
        $td = new TaskDuplicationModel($this->container);

        $values = array('date_due' => 0);
        $td->calculateRecurringTaskDueDate($values);
        $this->assertEquals(0, $values['date_due']);

        $values = array('date_due' => 0, 'recurrence_factor' => 0, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_TRIGGERDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $td->calculateRecurringTaskDueDate($values);
        $this->assertEquals(0, $values['date_due']);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => 1, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_TRIGGERDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $td->calculateRecurringTaskDueDate($values);
        $this->assertEquals(time() + 86400, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => -2, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_TRIGGERDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $td->calculateRecurringTaskDueDate($values);
        $this->assertEquals(time() - 2 * 86400, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => 1, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_DUEDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $td->calculateRecurringTaskDueDate($values);
        $this->assertEquals(1431291376 + 86400, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => -1, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_DUEDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS);
        $td->calculateRecurringTaskDueDate($values);
        $this->assertEquals(1431291376 - 86400, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => 2, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_DUEDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_MONTHS);
        $td->calculateRecurringTaskDueDate($values);
        $this->assertEquals(1436561776, $values['date_due'], '', 1);

        $values = array('date_due' => 1431291376, 'recurrence_factor' => 2, 'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_DUEDATE, 'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_YEARS);
        $td->calculateRecurringTaskDueDate($values);
        $this->assertEquals(1494449776, $values['date_due'], '', 1);
    }

    public function testDuplicateRecurringTask()
    {
        $td = new TaskDuplicationModel($this->container);
        $tc = new TaskCreationModel($this->container);
        $tf = new TaskFinderModel($this->container);
        $p = new ProjectModel($this->container);
        $dp = new DateParser($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test1')));

        $this->assertEquals(1, $tc->create(array(
            'title' => 'test',
            'project_id' => 1,
            'date_due' => 1436561776,
            'recurrence_status' => TaskModel::RECURRING_STATUS_PENDING,
            'recurrence_trigger' => TaskModel::RECURRING_TRIGGER_CLOSE,
            'recurrence_factor' => 2,
            'recurrence_timeframe' => TaskModel::RECURRING_TIMEFRAME_DAYS,
            'recurrence_basedate' => TaskModel::RECURRING_BASEDATE_TRIGGERDATE,
        )));

        $this->assertEquals(2, $td->duplicateRecurringTask(1));

        $task = $tf->getById(1);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::RECURRING_STATUS_PROCESSED, $task['recurrence_status']);
        $this->assertEquals(2, $task['recurrence_child']);
        $this->assertEquals(1436486400, $task['date_due'], '', 2);

        $task = $tf->getById(2);
        $this->assertNotEmpty($task);
        $this->assertEquals(TaskModel::RECURRING_STATUS_PENDING, $task['recurrence_status']);
        $this->assertEquals(TaskModel::RECURRING_TRIGGER_CLOSE, $task['recurrence_trigger']);
        $this->assertEquals(TaskModel::RECURRING_TIMEFRAME_DAYS, $task['recurrence_timeframe']);
        $this->assertEquals(TaskModel::RECURRING_BASEDATE_TRIGGERDATE, $task['recurrence_basedate']);
        $this->assertEquals(1, $task['recurrence_parent']);
        $this->assertEquals(2, $task['recurrence_factor']);
        $this->assertEquals($dp->removeTimeFromTimestamp(strtotime('+2 days')), $task['date_due'], '', 2);
    }
}
