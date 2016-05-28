<?php

use Kanboard\Filter\ProjectActivityProjectIdsFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectActivityModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class ProjectActivityProjectIdsFilterTest extends Base
{
    public function testFilterByProjectIds()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);
        $query = $projectActivityModel->getQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'P2')));
        $this->assertEquals(3, $projectModel->create(array('name' => 'P3')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 2)));
        $this->assertEquals(3, $taskCreation->create(array('title' => 'Test', 'project_id' => 3)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(1))));
        $this->assertNotFalse($projectActivityModel->createEvent(2, 2, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(2))));
        $this->assertNotFalse($projectActivityModel->createEvent(3, 3, 1, TaskModel::EVENT_CREATE, array('task' => $taskFinder->getById(3))));

        $filter = new ProjectActivityProjectIdsFilter(array(1, 2));
        $filter->withQuery($query)->apply();
        $this->assertCount(2, $query->findAll());
    }

    public function testWithEmptyArgument()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $projectActivityModel = new ProjectActivityModel($this->container);
        $query = $projectActivityModel->getQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'P2')));
        $this->assertEquals(3, $projectModel->create(array('name' => 'P3')));

        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test', 'project_id' => 2)));
        $this->assertEquals(3, $taskCreation->create(array('title' => 'Test', 'project_id' => 3)));

        $this->assertNotFalse($projectActivityModel->createEvent(1, 1, 1, TaskModel::EVENT_CREATE, $taskFinder->getById(1)));
        $this->assertNotFalse($projectActivityModel->createEvent(2, 2, 1, TaskModel::EVENT_CREATE, $taskFinder->getById(2)));
        $this->assertNotFalse($projectActivityModel->createEvent(3, 3, 1, TaskModel::EVENT_CREATE, $taskFinder->getById(3)));

        $filter = new ProjectActivityProjectIdsFilter(array());
        $filter->withQuery($query)->apply();
        $this->assertCount(0, $query->findAll());
    }
}
