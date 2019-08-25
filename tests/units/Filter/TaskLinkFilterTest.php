<?php

use Kanboard\Filter\TaskLinkFilter;
use Kanboard\Model\LinkModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;

require_once __DIR__.'/../Base.php';

class TaskLinkFilterTest extends Base
{
    public function testMatchLabel()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $linkModel = new LinkModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test 1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test 2', 'project_id' => 1)));

        $links = $linkModel->getMergedList();

        $this->assertNotFalse(1, $taskLinkModel->create(1, 2, $links[1]['id']));

        $filter = new TaskLinkFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue($links[1]["label"]);
        $filter->apply();

        $result = $query->findAll();
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result[0]["id"]);
    }

    public function testMatchOppositeLabel()
    {
        $taskFinder = new TaskFinderModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $linkModel = new LinkModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Test 1', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Test 2', 'project_id' => 1)));

        $links = $linkModel->getMergedList();

        $this->assertNotFalse(1, $taskLinkModel->create(1, 2, $links[1]['id']));

        $filter = new TaskLinkFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue($links[1]["opposite_label"]);
        $filter->apply();

        $result = $query->findAll();
        $this->assertCount(1, $result);
        $this->assertEquals(2, $result[0]["id"]);
    }
}
