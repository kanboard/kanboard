<?php

use Kanboard\Filter\TaskCreatorFilter;
use Kanboard\Model\Project;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\User;

require_once __DIR__.'/../Base.php';

class TaskCreatorFilterTest extends Base
{
    public function testWithIntegerAssigneeId()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'creator_id' => 1)));

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue(1);
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue(123);
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithStringAssigneeId()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'creator_id' => 1)));

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue('1');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue("123");
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithUsername()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'creator_id' => 1)));

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue('admin');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue('foobar');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithName()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $userModel = new User($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(2, $userModel->create(array('username' => 'foobar', 'name' => 'Foo Bar')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'creator_id' => 2)));

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue('foo bar');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue('bob');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithNobody()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $filter = new TaskCreatorFilter();
        $filter->withQuery($query);
        $filter->withValue('nobody');
        $filter->apply();

        $this->assertCount(1, $query->findAll());
    }

    public function testWithCurrentUser()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'creator_id' => 1)));

        $filter = new TaskCreatorFilter();
        $filter->setCurrentUserId(1);
        $filter->withQuery($query);
        $filter->withValue('me');
        $filter->apply();

        $this->assertCount(1, $query->findAll());

        $filter = new TaskCreatorFilter();
        $filter->setCurrentUserId(2);
        $filter->withQuery($query);
        $filter->withValue('me');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }
}
