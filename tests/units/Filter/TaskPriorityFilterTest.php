<?php

use Kanboard\Filter\TaskPriorityFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;

require_once __DIR__.'/../Base.php';

class TaskPriorityFilterTest extends Base
{
    public function testWithDefinedPriority()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'priority' => 2)));

        $filter = new TaskPriorityFilter();
        $filter->withQuery($query);
        $filter->withValue(2);
        $filter->apply();

        $this->assertCount(1, $query->findAll());
    }

    public function testWithNoPriority()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $filter = new TaskPriorityFilter();
        $filter->withQuery($query);
        $filter->withValue(2);
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }
}
