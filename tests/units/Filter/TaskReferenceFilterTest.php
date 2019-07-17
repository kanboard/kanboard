<?php

use Kanboard\Filter\TaskReferenceFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;

require_once __DIR__.'/../Base.php';

class TaskReferenceFilterTest extends Base
{
    public function testWithoutMatch()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));

        $filter = new TaskReferenceFilter();
        $filter->withQuery($query);
        $filter->withValue('aaa-bbb');
        $filter->apply();

        $this->assertCount(0, $query->findAll());
    }

    public function testWithExactMatch()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'reference' => 'aaa-bbb')));

        $filter = new TaskReferenceFilter();
        $filter->withQuery($query);
        $filter->withValue('aaa-bbb');
        $filter->apply();

        $this->assertCount(1, $query->findAll());
    }

    public function testWithWildCard()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1, 'reference' => 'aaa-bbb')));

        $filter = new TaskReferenceFilter();
        $filter->withQuery($query);
        $filter->withValue('aaa-*');
        $filter->apply();

        $this->assertCount(1, $query->findAll());
    }

    public function testWithNone()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'A', 'project_id' => 1, 'reference' => 'aaa-bbb')));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'B', 'project_id' => 1)));

        $filter = new TaskReferenceFilter();
        $filter->withQuery($query);
        $filter->withValue('none');
        $filter->apply();

        $this->assertCount(1, $query->findAll());
    }
}
