<?php

use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\UserModel;

require_once __DIR__.'/../Base.php';

class TaskAssigneeFilterTest extends Base
{
    public function testWithIntegerAssigneeId()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'owner_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
        $filter->withQuery($query);
        $filter->withValue(1);
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
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

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'owner_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
        $filter->withQuery($query);
        $filter->withValue('1');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
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

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'owner_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
        $filter->withQuery($query);
        $filter->withValue('admin');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
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
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'foobar', 'name' => 'Foo Bar')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'owner_id' => 2)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
        $filter->withQuery($query);
        $filter->withValue('foo bar');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
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

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
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

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'owner_id' => 1)));

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
        $filter->setCurrentUserId(1);
        $filter->withQuery($query);
        $filter->withValue('me');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $query = $taskFinder->getExtendedQuery();
        $filter = new TaskAssigneeFilter();
        $filter->setCurrentUserId(2);
        $filter->withQuery($query);
        $filter->withValue('me');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }
}
