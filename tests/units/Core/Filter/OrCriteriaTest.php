<?php

use Kanboard\Core\Filter\OrCriteria;
use Kanboard\Filter\TaskAssigneeFilter;
use Kanboard\Filter\TaskTitleFilter;
use Kanboard\Model\Project;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskFinder;
use Kanboard\Model\User;

require_once __DIR__.'/../../Base.php';

class OrCriteriaTest extends Base
{
    public function testWithSameFilter()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $userModel = new User($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(2, $userModel->create(array('username' => 'foobar', 'name' => 'Foo Bar')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'owner_id' => 2)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'owner_id' => 1)));

        $criteria = new OrCriteria();
        $criteria->withQuery($query);
        $criteria->withFilter(TaskAssigneeFilter::getInstance(1));
        $criteria->withFilter(TaskAssigneeFilter::getInstance(2));
        $criteria->apply();

        $this->assertCount(2, $query->findAll());
    }

    public function testWithDifferentFilter()
    {
        $taskFinder = new TaskFinder($this->container);
        $taskCreation = new TaskCreation($this->container);
        $projectModel = new Project($this->container);
        $userModel = new User($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(2, $userModel->create(array('username' => 'foobar', 'name' => 'Foo Bar')));
        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'ABC', 'project_id' => 1, 'owner_id' => 2)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'DEF', 'project_id' => 1, 'owner_id' => 1)));

        $criteria = new OrCriteria();
        $criteria->withQuery($query);
        $criteria->withFilter(TaskAssigneeFilter::getInstance(1));
        $criteria->withFilter(TaskTitleFilter::getInstance('ABC'));
        $criteria->apply();

        $this->assertCount(2, $query->findAll());
    }
}
