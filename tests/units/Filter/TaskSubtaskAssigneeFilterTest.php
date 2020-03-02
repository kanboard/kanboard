<?php

use Kanboard\Filter\TaskSubtaskAssigneeFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\SubtaskModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\UserModel;

require_once __DIR__.'/../Base.php';

class TaskSubtaskAssigneeFilterTest extends Base
{
    public function testWithIntegerAssigneeId()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask', 'task_id' => 1, 'user_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue(1);
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue(123);
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithStringAssigneeId()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask', 'task_id' => 1, 'user_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('1');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue("123");
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithUsername()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask', 'task_id' => 1, 'user_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('admin');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('foobar');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithName()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'foobar', 'name' => 'Foo Bar')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask', 'task_id' => 1, 'user_id' => 2)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('foo bar');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('bob');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithNobody()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask', 'task_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('nobody');
        $filter->apply();

        $this->assertCount(1, $query->findAll());
    }

    public function testWithCurrentUser()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask', 'task_id' => 1, 'user_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->setCurrentUserId(1);
        $filter->withQuery($query);
        $filter->withValue('me');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->setCurrentUserId(2);
        $filter->withQuery($query);
        $filter->withValue('me');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithMultiple()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $subtaskModel = new SubtaskModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'foobar', 'name' => 'Foo Bar')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $subtaskModel->create(array('title' => 'subtask #1', 'task_id' => 1, 'user_id' => 2)));
        $this->assertEquals(2, $subtaskModel->create(array('title' => 'subtask #2', 'task_id' => 1, 'user_id' => 1)));

        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test 2', 'project_id' => 1)));
        $this->assertEquals(3, $subtaskModel->create(array('title' => 'subtask #3', 'task_id' => 2)));
        $this->assertEquals(4, $subtaskModel->create(array('title' => 'subtask #4', 'task_id' => 2, 'user_id' => 1)));

        $this->assertEquals(3, $taskCreation->create(array('title' => 'Test 3', 'project_id' => 1)));
        $this->assertEquals(5, $subtaskModel->create(array('title' => 'subtask #5', 'task_id' => 3)));
        $this->assertEquals(6, $subtaskModel->create(array('title' => 'subtask #6', 'task_id' => 3, 'user_id' => 2)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('foo bar');
        $filter->apply();

        $this->assertCount(2, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('nobody');
        $filter->apply();
        $result = $query->findAll();
        $this->assertCount(2, $result);

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskSubtaskAssigneeFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue('admin');
        $filter->apply();

        $this->assertCount(2, $query->findAll());
    }
}
